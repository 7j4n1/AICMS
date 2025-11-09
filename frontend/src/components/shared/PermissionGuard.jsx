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
  fallback = null 
}) {
  const { hasRole, hasPermission, isAdmin } = usePermission();

  // Check admin requirement
  if (requireAdmin && !isAdmin()) {
    return fallback;
  }

  // Check role requirement
  if (requireRole && !hasRole(requireRole)) {
    return fallback;
  }

  // Check permission requirement
  if (requirePermission && !hasPermission(requirePermission)) {
    return fallback;
  }

  return <>{children}</>;
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
