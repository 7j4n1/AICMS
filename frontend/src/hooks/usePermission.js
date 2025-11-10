// Import Dependencies
import { useAuthContext } from "app/contexts/auth/context";

/**
 * Hook to check user permissions and roles
 */
export function usePermission() {
  const { user } = useAuthContext();

  const hasRole = (role) => {
    if (!user) return false;
    
    // Check if user has a role property (admin)
    if (user.roles) {
      // Both role and user.roles are arrays
      if(Array.isArray(role) && Array.isArray(user.roles)) {
        return role.some(r => user.roles.includes(r));
      }
      // role is string and user.roles is array
      if(typeof role === 'string' && Array.isArray(user.roles)) {
        return user.roles.includes(role);
      }
      // role is array and user.role is string
      if(Array.isArray(role) && typeof user.role === 'string') {
        return role.includes(user.role);
      }

      // Both are strings
      return user.role === role;
    }
    
    return false;
  };

  const hasPermission = (permission) => {
    if (!user) return false;
    
    // Check Spatie permissions
    if (user.permissions && Array.isArray(user.permissions)) {
      const userPermissions = user.permissions.map(p => p.name);
      if (Array.isArray(permission)) {
        return permission.some(p => userPermissions.includes(p));
      }
      return userPermissions.includes(permission);
    }
    
    return false;
  };

  const isAdmin = () => hasRole(['admin', 'super-admin', 'manager']);
  const isSuperAdmin = () => hasRole('super-admin');
  const isManager = () => hasRole('manager');
  const isMember = () => hasRole('member') || (user && user.coopId);

  // Permission checks for specific actions
  const canCreate = () => hasPermission('can create');
  const canEdit = () => hasPermission('can edit');
  const canView = () => hasPermission('can view');
  const canDelete = () => hasPermission('can delete');
  const canOnlyView = () => hasPermission('can only view');

  // Feature-specific permission checks
  const canManageSystem = () => isSuperAdmin();
  const canManageMembers = () => isAdmin();
  const canApprovePayments = () => isAdmin();
  const canManageTickets = () => isAdmin();
  const canConfigureSystem = () => isSuperAdmin();
  const canManagePaymentGateways = () => isSuperAdmin();

  

  return {
    user,
    hasRole,
    hasPermission,
    isAdmin,
    isMember,
    isSuperAdmin,
    isManager,
    canCreate,
    canEdit,
    canView,
    canDelete,
    canOnlyView,
    canManageSystem,
    canManageMembers,
    canApprovePayments,
    canManageTickets,
    canManagePaymentGateways,
    canConfigureSystem,
  };
}
