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
    if (user.role) {
      if (Array.isArray(role)) {
        return role.includes(user.role);
      }
      return user.role === role;
    }
    
    // Check if user has roles from Spatie (array of role objects)
    if (user.roles && Array.isArray(user.roles)) {
      const userRoles = user.roles.map(r => r.name);
      if (Array.isArray(role)) {
        return role.some(r => userRoles.includes(r));
      }
      return userRoles.includes(role);
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

  const isAdmin = () => {
    return hasRole(['admin', 'super-admin', 'manager']);
  };

  const isMember = () => {
    // Members typically have coopId but no role
    return hasRole('member') || (user && user.coopId);
  };

  const canApprovePayments = () => {
    return hasPermission('approve-payments') || isAdmin();
  };

  const canManageTickets = () => {
    return hasPermission('manage-tickets') || isAdmin();
  };

  const canConfigureSystem = () => {
    return hasPermission('configure-system') || hasRole(['admin', 'super-admin']);
  };

  return {
    user,
    hasRole,
    hasPermission,
    isAdmin,
    isMember,
    canApprovePayments,
    canManageTickets,
    canConfigureSystem,
  };
}
