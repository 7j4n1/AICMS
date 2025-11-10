// Import Dependencies
import { useLocation } from "react-router";
import { useMemo, useRef, useState } from "react";
import {
  useDidUpdate,
  useIsomorphicEffect,
} from "hooks";
import SimpleBar from "simplebar-react";

// Local Imports
import { navigation } from "app/navigation";
import { Group } from "./Group";
import { Accordion } from "components/ui";
import { isRouteActive } from "utils/isRouteActive";
import { usePermission } from "hooks/usePermission";
import { filterNavigationByPermission } from "utils/filterNavigationByPermission";

// ----------------------------------------------------------------------

export function Menu() {
  const { pathname } = useLocation();
  const { ref } = useRef();
  const permissionHook = usePermission();

  // Filter navigation based on permissions
  const filteredNavigation = useMemo(
    () => filterNavigationByPermission(navigation, permissionHook),
    [permissionHook]
  );

  const activeGroup = filteredNavigation.find((item) => {
    if (item.path) return isRouteActive(item.path, pathname);
  });

  const activeCollapsible = activeGroup?.childs?.find((item) => {
    if (item.path) return isRouteActive(item.path, pathname);
  });

  const [expanded, setExpanded] = useState(activeCollapsible?.path || null);

  useDidUpdate(() => {
    activeCollapsible?.path !== expanded &&
      setExpanded(activeCollapsible?.path);
  }, [activeCollapsible?.path]);

  useIsomorphicEffect(() => {
    const activeItem = ref?.current.querySelector("[data-menu-active=true]");
    activeItem?.scrollIntoView({ block: "center" });
  }, []);

  return (
    <SimpleBar
      scrollableNodeProps={{ ref }}
      className="h-full overflow-x-hidden pb-6"
    >
      <Accordion value={expanded} onChange={setExpanded} className="space-y-1">
        {filteredNavigation.map((nav) => (
          <Group key={nav.id} data={nav} />
        ))}
      </Accordion>
    </SimpleBar>
  );
}
