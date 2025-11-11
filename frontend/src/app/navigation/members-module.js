import { usePermission } from "hooks/usePermission";
import { NAV_TYPE_ROOT, NAV_TYPE_ITEM } from 'constants/app.constant';
import { UsersIcon } from "@heroicons/react/24/outline";

const ROOT_PATH = '/';

const path = (root, item) => `${root}${item}`;

export function MembersItems() {
    const { isAdmin } = usePermission();

    const membersItems = [
        {
            id: 'members',
            type: NAV_TYPE_ROOT,
            path: '/members',
            title: 'Members',
            transKey: 'nav.members.members',
            Icon: UsersIcon,
            visible: isAdmin(),
            childs: [
            {
                id: 'members.list',
                path: path(ROOT_PATH, 'members'),
                type: NAV_TYPE_ITEM,
                title: 'All Members',
                transKey: 'nav.members.list',
                Icon: UsersIcon,
                visible: isAdmin(),
            },
            {
                id: 'members.create',
                path: path(ROOT_PATH, 'members/create'),
                type: NAV_TYPE_ITEM,
                title: 'Add Member',
                transKey: 'nav.members.create',
                Icon: UsersIcon,
                visible: isAdmin(),
            },

            ],
        }   
    ];

    return membersItems.filter(item => item.visible);
};