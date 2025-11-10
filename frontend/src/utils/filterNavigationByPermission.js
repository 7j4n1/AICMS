/**
 * Filter navigation items based on user permissions and roles
 * @param {Array} navigation - Array of navigation items
 * @param {Object} permissionHook - Object from usePermission hook
 * @returns {Array} Filtered navigation items
 */
export function filterNavigationByPermission(navigation, permissionHook) {
  if (!navigation || !Array.isArray(navigation)) return [];
  
  const { hasRole, hasPermission, isAdmin, isMember } = permissionHook;

  const checkAccess = (item) => {
    // No permission requirements means visible to all
    if (!item.requireRole && !item.requirePermission && !item.requireAdmin && !item.requireMember) {
      return true;
    }

    // Check admin requirement
    if (item.requireAdmin && !isAdmin()) {
      return false;
    }

    // Check member requirement
    if (item.requireMember && !isMember()) {
      return false;
    }

    // Check role requirement
    if (item.requireRole && !hasRole(item.requireRole)) {
      return false;
    }

    // Check permission requirement
    if (item.requirePermission && !hasPermission(item.requirePermission)) {
      return false;
    }

    return true;
  };

  return navigation
    .filter(checkAccess)
    .map(item => {
      // Filter children if they exist
      if (item.childs && Array.isArray(item.childs)) {
        const filteredChilds = item.childs.filter(checkAccess);
        
        // Only include parent if it has accessible children or no children
        if (filteredChilds.length > 0 || !item.childs.length) {
          return {
            ...item,
            childs: filteredChilds
          };
        }
        return null;
      }
      return item;
    })
    .filter(Boolean); // Remove null items
}
