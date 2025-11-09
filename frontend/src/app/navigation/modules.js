import { 
 UsersIcon, 
 BanknotesIcon, 
 CurrencyDollarIcon,
 CalendarIcon,
 TagIcon,
 ShoppingBagIcon,
 UserGroupIcon,
 LifebuoyIcon,
 BellAlertIcon,
 Cog6ToothIcon
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
   {
     id: 'members.create',
     path: path(ROOT_PATH, 'members/create'),
     type: NAV_TYPE_ITEM,
     title: 'Add Member',
     transKey: 'nav.members.create',
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

// Support Tickets Navigation
export const supportTickets = {
 id: 'support-tickets',
 type: NAV_TYPE_ROOT,
 path: '/support-tickets',
 title: 'Support',
 transKey: 'nav.support.tickets',
 Icon: LifebuoyIcon,
 childs: [
   {
     id: 'support-tickets.list',
     path: path(ROOT_PATH, 'support-tickets'),
     type: NAV_TYPE_ITEM,
     title: 'All Tickets',
     transKey: 'nav.support.list',
     Icon: LifebuoyIcon,
   },
   {
     id: 'support-tickets.create',
     path: path(ROOT_PATH, 'support-tickets/create'),
     type: NAV_TYPE_ITEM,
     title: 'Create Ticket',
     transKey: 'nav.support.create',
     Icon: LifebuoyIcon,
   },
 ],
};

// Payment Notifications Navigation
export const paymentNotifications = {
 id: 'payment-notifications',
 type: NAV_TYPE_ROOT,
 path: '/payment-notifications',
 title: 'Payment Notifications',
 transKey: 'nav.paymentNotifications.title',
 Icon: BellAlertIcon,
 childs: [
   {
     id: 'payment-notifications.list',
     path: path(ROOT_PATH, 'payment-notifications'),
     type: NAV_TYPE_ITEM,
     title: 'All Notifications',
     transKey: 'nav.paymentNotifications.list',
     Icon: BellAlertIcon,
   },
   {
     id: 'payment-notifications.create',
     path: path(ROOT_PATH, 'payment-notifications/create'),
     type: NAV_TYPE_ITEM,
     title: 'Submit Notification',
     transKey: 'nav.paymentNotifications.create',
     Icon: BellAlertIcon,
   },
 ],
};

// Admin Configuration Navigation
export const adminConfig = {
 id: 'admin-config',
 type: NAV_TYPE_ROOT,
 path: '/admin-config',
 title: 'Configuration',
 transKey: 'nav.adminConfig.title',
 Icon: Cog6ToothIcon,
 childs: [
   {
     id: 'admin-config.system',
     path: path(ROOT_PATH, 'admin-config/system'),
     type: NAV_TYPE_ITEM,
     title: 'System Settings',
     transKey: 'nav.adminConfig.system',
     Icon: Cog6ToothIcon,
   },
   {
     id: 'admin-config.payment-gateways',
     path: path(ROOT_PATH, 'admin-config/payment-gateways'),
     type: NAV_TYPE_ITEM,
     title: 'Payment Gateways',
     transKey: 'nav.adminConfig.paymentGateways',
     Icon: Cog6ToothIcon,
   },
   {
     id: 'admin-config.savings-types',
     path: path(ROOT_PATH, 'admin-config/savings-types'),
     type: NAV_TYPE_ITEM,
     title: 'Savings Types',
     transKey: 'nav.adminConfig.savingsTypes',
     Icon: Cog6ToothIcon,
   },
   {
     id: 'admin-config.loan-eligibility',
     path: path(ROOT_PATH, 'admin-config/loan-eligibility'),
     type: NAV_TYPE_ITEM,
     title: 'Loan Eligibility',
     transKey: 'nav.adminConfig.loanEligibility',
     Icon: Cog6ToothIcon,
   },
 ],
};