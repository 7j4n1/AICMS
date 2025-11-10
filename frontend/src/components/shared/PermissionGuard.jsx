// Import Dependencies
import PropTypes from "prop-types";

// Local Imports
import { usePermission } from "hooks/usePermission";

/**
 * Permission-based rendering component
 * Only renders children if user has the required permissions or roles
 */
export function PermissionGuard({ 
  children, 
  requireRole, 
  requirePermission,
  requireAdmin,
  requireSuperAdmin,
  fallback = null 
}) {
  const { hasRole, hasPermission, isAdmin, isSuperAdmin } = usePermission();

  // Check admin requirement
  if (requireSuperAdmin && !isSuperAdmin()) return fallback;
  if (requireAdmin && !isAdmin()) return fallback;
  // Check role requirement
  if (requireRole && !hasRole(requireRole)) return fallback;
  // Check permission requirement
  if (requirePermission && !hasPermission(requirePermission)) return fallback;

  return <>{children}</>;
}

// Admin-only guard
export function AdminGuard({ children, fallback = null }) {
  return (
    <PermissionGuard requireAdmin fallback={fallback}>
      {children}
    </PermissionGuard>
  );
}

// Super Admin-only guard
export function SuperAdminGuard({ children, fallback = null }) {
  return (
    <PermissionGuard requireSuperAdmin fallback={fallback}>
      {children}
    </PermissionGuard>
  );
}

// Member-only guard
export function MemberGuard({ children, fallback = null }) {
  return (
    <PermissionGuard requireRole="member" fallback={fallback}>
      {children}
    </PermissionGuard>
  );
}

PermissionGuard.propTypes = {
  children: PropTypes.node.isRequired,
  requireRole: PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.arrayOf(PropTypes.string),
  ]),
  requirePermission: PropTypes.oneOfType([
    PropTypes.string,
    PropTypes.arrayOf(PropTypes.string),
  ]),
  requireAdmin: PropTypes.bool,
  fallback: PropTypes.node,
};
