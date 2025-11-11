# Navigation Guards & Role-Based Sidebar Filtering Guide

## Overview

The AICMS application now features dynamic navigation filtering based on user roles and permissions. The sidebar navigation automatically adjusts to show only the menu items that the logged-in user has permission to access.

## Features

### 1. Dynamic Navigation Filtering
- Navigation items are filtered in real-time based on user's role and permissions
- Both parent menu items and child items can have separate permission requirements
- Navigation updates automatically when user permissions change

### 2. Role-Based Access Control
- **Admin Access**: Members, Loans, Payments, Annual Fees, Business, System Configuration
- **Super Admin Access**: Admin Management, Payment Gateways (within System Configuration)
- **All Users**: Support Tickets, Payment Notifications
- **Member Access**: Support Tickets (create, view own), Payment Notifications (submit, view own)

## Implementation Details

### Navigation Permission Properties

Each navigation item can have the following permission properties:

```javascript
{
  id: 'unique-id',
  title: 'Navigation Title',
  path: '/path',
  Icon: IconComponent,
  
  // Permission properties (optional)
  requireAdmin: true,           // Requires admin, super-admin, or manager role
  requireMember: true,           // Requires member role
  requireRole: 'role-name',      // Requires specific role(s) - string or array
  requirePermission: 'permission', // Requires specific permission(s) - string or array
  
  childs: [/* child items with their own permissions */]
}
```

### Current Navigation Permissions

#### 1. Members Module
```javascript
requireAdmin: true
```
- Visible only to admins (admin, super-admin, manager)
- Contains: All Members, Add Member

#### 2. Loans Module
```javascript
requireAdmin: true
```
- Visible only to admins
- Contains: All Loans, Active Loans

#### 3. Payments Module
```javascript
requireAdmin: true
```
- Visible only to admins
- Contains: All Payments

#### 4. Annual Fees Module
```javascript
requireAdmin: true
```
- Visible only to admins
- Contains: All Annual Fees

#### 5. Business Module
```javascript
requireAdmin: true
```
- Visible only to admins
- Contains: Categories, Items

#### 6. Admin Management
```javascript
requireRole: 'super-admin'
```
- Visible only to super admins
- Contains: All Admins

#### 7. Support Tickets
```javascript
// No requirements - visible to all authenticated users
```
- Visible to both admins and members
- Contains: All Tickets, Create Ticket

#### 8. Payment Notifications
```javascript
// No requirements - visible to all authenticated users
```
- Visible to both admins and members
- Contains: All Notifications, Submit Notification

#### 9. System Configuration
```javascript
requireAdmin: true
```
- Parent menu visible to admins
- Contains:
  - General Settings (admin)
  - Payment Gateways (super-admin only)
  - Savings Types (admin)
  - Loan Eligibility (admin)

## How It Works

### 1. Navigation Definition (modules.js)

Navigation items are defined with permission requirements:

```javascript
export const members = {
  id: 'members',
  type: NAV_TYPE_ROOT,
  path: '/members',
  title: 'Members',
  Icon: UsersIcon,
  requireAdmin: true, // Permission requirement
  childs: [/* ... */]
};
```

### 2. Permission Filtering (filterNavigationByPermission.js)

The utility function filters navigation based on user permissions:

```javascript
import { filterNavigationByPermission } from 'utils/filterNavigationByPermission';

const filteredNav = filterNavigationByPermission(navigation, permissionHook);
```

**Features:**
- Filters parent items based on their permission requirements
- Filters child items independently
- Removes parent items with no accessible children
- Maintains navigation structure

### 3. Sidebar Integration (Sidebar/index.jsx)

The sidebar uses the filtered navigation:

```javascript
export function Sidebar() {
  const permissionHook = usePermission();
  
  // Filter navigation based on user permissions
  const filteredNavigation = useMemo(
    () => filterNavigationByPermission(navigation, permissionHook),
    [permissionHook.user]
  );
  
  return (
    <>
      <MainPanel nav={filteredNavigation} />
      <PrimePanel currentSegment={currentSegment} />
    </>
  );
}
```

## Permission Hook (usePermission)

The hook provides role and permission checking functions:

```javascript
const {
  user,                    // Current user object
  hasRole,                 // Check if user has role(s)
  hasPermission,           // Check if user has permission(s)
  isAdmin,                 // Check if user is admin/super-admin/manager
  isMember,                // Check if user is member
  isSuperAdmin,            // Check if user is super-admin
  isManager,               // Check if user is manager
  canManageSystem,         // Super admin only
  canManageMembers,        // Admin only
  canApprovePayments,      // Admin only
  canManageTickets,        // Admin only
  canConfigureSystem,      // Super admin only
  canManagePaymentGateways // Super admin only
} = usePermission();
```

## Adding New Navigation Items

### Step 1: Define Navigation Item

Add to `frontend/src/app/navigation/modules.js`:

```javascript
export const myModule = {
  id: 'my-module',
  type: NAV_TYPE_ROOT,
  path: '/my-module',
  title: 'My Module',
  Icon: MyIcon,
  requireAdmin: true, // or requireRole, requirePermission, requireMember
  childs: [
    {
      id: 'my-module.list',
      path: '/my-module',
      type: NAV_TYPE_ITEM,
      title: 'List Items',
      Icon: MyIcon,
      // Optional: different permission for child
      requirePermission: 'view-items'
    }
  ]
};
```

### Step 2: Add to Navigation Array

In `frontend/src/app/navigation/index.js`:

```javascript
import { myModule } from './modules';

export const navigation = [
  dashboards,
  members,
  myModule, // Add here
  // ... other modules
];
```

### Step 3: Test

The navigation will automatically filter based on the defined permissions.

## Page-Level Protection

In addition to navigation filtering, protect routes with guards:

```javascript
import { PermissionGuard } from 'components/shared/PermissionGuard';

export function MyPage() {
  return (
    <PermissionGuard requireAdmin fallback={<Unauthorized />}>
      <div>Admin-only content</div>
    </PermissionGuard>
  );
}
```

## Testing Different Roles

### Admin View
Navigation shows:
- Dashboard
- Members
- Loans
- Payments
- Annual Fees
- Business
- Support Tickets
- Payment Notifications
- System Configuration (all sub-items except Payment Gateways if not super-admin)

### Super Admin View
Navigation shows:
- All admin items
- Admin Management
- System Configuration (including Payment Gateways)

### Member View
Navigation shows:
- Dashboard
- Support Tickets
- Payment Notifications

## Best Practices

1. **Consistent Permissions**: Use the same permission requirements for both navigation and page content
2. **Fallback Content**: Provide fallback UI for unauthorized access
3. **Route Guards**: Combine navigation filtering with route-level guards
4. **Permission Granularity**: Set permissions at the appropriate level (parent vs child)
5. **User Experience**: Ensure members see relevant features clearly

## Security Notes

⚠️ **Important**: Navigation filtering is a UX feature, not a security feature.

- Always implement server-side authorization
- Backend API should validate permissions for every request
- Frontend filtering only improves user experience
- Route guards provide additional client-side protection

## Troubleshooting

### Navigation Item Not Showing

1. Check user's role: `console.log(user.role, user.roles)`
2. Check permission requirements in navigation definition
3. Verify `usePermission` hook returns correct values
4. Check filter logic in `filterNavigationByPermission.js`

### Navigation Updates Not Reflecting

1. Ensure `permissionHook.user` is in the dependency array
2. Check if user object updates on login/logout
3. Verify navigation array is properly imported

### Child Items Not Filtering

1. Check if child has separate permission requirement
2. Verify parent permission doesn't block all children
3. Check filter logic handles nested children

## Files Modified

1. `frontend/src/utils/filterNavigationByPermission.js` - NEW: Filter utility
2. `frontend/src/app/navigation/modules.js` - MODIFIED: Added permission properties
3. `frontend/src/app/layouts/MainLayout/Sidebar/index.jsx` - MODIFIED: Integrated filtering
4. `frontend/src/hooks/usePermission.js` - EXISTING: Permission checking hook
5. `frontend/src/components/shared/PermissionGuard.jsx` - EXISTING: Component guard

## Summary

The navigation guard system provides:
- ✅ Dynamic sidebar filtering based on user permissions
- ✅ Role-based access control (Admin, Super Admin, Member)
- ✅ Permission-based granular control
- ✅ Automatic updates when permissions change
- ✅ Consistent UX across the application
- ✅ Easy to extend with new modules
- ✅ Works with existing PermissionGuard component

The sidebar now intelligently shows only the navigation items relevant to the logged-in user's role and permissions, creating a cleaner and more secure user experience.
