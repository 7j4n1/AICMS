import { 
 UsersIcon, 
 BanknotesIcon, 
 CurrencyDollarIcon,
 CalendarIcon,
 TagIcon,
 ShoppingBagIcon,
 UserGroupIcon
} from '@heroicons/react/24/outline';
import { NAV_TYPE_ROOT, NAV_TYPE_ITEM } from 'constants/app.constant';

const ROOT_PATH = '/';

const path = (root, item) => `${root}${item}`;

// Members Navigation
export const members = {
 id: 'members',
 type: NAV_TYPE_ROOT,
 path: '/members',
 title: 'Members',
 transKey: 'nav.members.members',
 Icon: UsersIcon,
 childs: [
   {
     id: 'members.list',
     path: path(ROOT_PATH, 'members'),
     type: NAV_TYPE_ITEM,
     title: 'All Members',
     transKey: 'nav.members.list',
     Icon: UsersIcon,
   },
 ],
};

// Loans Navigation
export const loans = {
 id: 'loans',
 type: NAV_TYPE_ROOT,
 path: '/loans',
 title: 'Loans',
 transKey: 'nav.loans.loans',
 Icon: BanknotesIcon,
 childs: [
   {
     id: 'loans.list',
     path: path(ROOT_PATH, 'loans'),
     type: NAV_TYPE_ITEM,
     title: 'All Loans',
     transKey: 'nav.loans.list',
     Icon: BanknotesIcon,
   },
   {
     id: 'loans.active',
     path: path(ROOT_PATH, 'loans/active'),
     type: NAV_TYPE_ITEM,
     title: 'Active Loans',
     transKey: 'nav.loans.active',
     Icon: BanknotesIcon,
   },
 ],
};

// Payments Navigation
export const payments = {
 id: 'payments',
 type: NAV_TYPE_ROOT,
 path: '/payments',
 title: 'Payments',
 transKey: 'nav.payments.payments',
 Icon: CurrencyDollarIcon,
 childs: [
   {
     id: 'payments.list',
     path: path(ROOT_PATH, 'payments'),
     type: NAV_TYPE_ITEM,
     title: 'All Payments',
     transKey: 'nav.payments.list',
     Icon: CurrencyDollarIcon,
   },
 ],
};

// Annual Fees Navigation
export const annualFees = {
 id: 'annual-fees',
 type: NAV_TYPE_ROOT,
 path: '/annual-fees',
 title: 'Annual Fees',
 transKey: 'nav.annualFees.annualFees',
 Icon: CalendarIcon,
 childs: [
   {
     id: 'annual-fees.list',
     path: path(ROOT_PATH, 'annual-fees'),
     type: NAV_TYPE_ITEM,
     title: 'All Annual Fees',
     transKey: 'nav.annualFees.list',
     Icon: CalendarIcon,
   },
 ],
};

// Business Items Navigation
export const business = {
 id: 'business',
 type: NAV_TYPE_ROOT,
 path: '/business',
 title: 'Business',
 transKey: 'nav.business.business',
 Icon: ShoppingBagIcon,
 childs: [
   {
     id: 'business.categories',
     path: path(ROOT_PATH, 'business/categories'),
     type: NAV_TYPE_ITEM,
     title: 'Categories',
     transKey: 'nav.business.categories',
     Icon: TagIcon,
   },
   {
     id: 'business.items',
     path: path(ROOT_PATH, 'business/items'),
     type: NAV_TYPE_ITEM,
     title: 'Items',
     transKey: 'nav.business.items',
     Icon: ShoppingBagIcon,
   },
 ],
};

// Admin Management Navigation
export const admins = {
 id: 'admins',
 type: NAV_TYPE_ROOT,
 path: '/admins',
 title: 'Admins',
 transKey: 'nav.admins.admins',
 Icon: UserGroupIcon,
 childs: [
   {
     id: 'admins.list',
     path: path(ROOT_PATH, 'admins'),
     type: NAV_TYPE_ITEM,
     title: 'All Admins',
     transKey: 'nav.admins.list',
     Icon: UserGroupIcon,
   },
 ],
};