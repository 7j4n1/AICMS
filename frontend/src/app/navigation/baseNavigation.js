import { NAV_TYPE_ITEM, } from "constants/app.constant";
import { UserCircleIcon } from '@heroicons/react/24/outline';
import DashboardsIcon from 'assets/dualicons/dashboards.svg?react'

export const baseNavigation = [
    {
        id: 'dashboards',
        type: NAV_TYPE_ITEM,
        path: '/dashboards',
        title: 'Dashboards',
        transKey: 'nav.dashboards.dashboards',
        Icon: DashboardsIcon,
    },
    {
        id: 'account',
        type: NAV_TYPE_ITEM,
        path: '/account',
        title: 'My Account',
        transKey: 'nav.account.profile',
        Icon: UserCircleIcon,
    },
]
