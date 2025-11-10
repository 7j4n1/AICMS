// Import Dependencies
import { useEffect, useState } from "react";
import { 
  UsersIcon, 
  BanknotesIcon, 
  CurrencyDollarIcon,
  BellAlertIcon,
  LifebuoyIcon,
  CheckCircleIcon,
  ClockIcon,
  PlusIcon
} from "@heroicons/react/24/outline";

// Local Imports
import { Page } from "components/shared/Page";
import { usePermission } from "hooks/usePermission";
import { StatCard } from "components/dashboard/StatCard";
import { QuickActionCard } from "components/dashboard/QuickActionCard";
import { PermissionGuard } from "components/shared/PermissionGuard";
import { 
  membersAPI, 
  loansAPI, 
  paymentsAPI,
  supportTicketsAPI,
  paymentNotificationsAPI,
  loanEligibilityAPI 
} from "services/api";
import { Card } from "components/ui";

// ----------------------------------------------------------------------

export default function Home() {
  const { isAdmin, isMember, user } = usePermission();
  const [stats, setStats] = useState({
    members: 0,
    activeLoans: 0,
    totalPayments: 0,
    pendingNotifications: 0,
    openTickets: 0,
  });
  const [memberData, setMemberData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardData();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const fetchDashboardData = async () => {
    try {
      setLoading(true);

      if (isAdmin()) {
        // Fetch admin dashboard data
        const [
          membersRes,
          loansRes,
          paymentsRes,
          ticketsRes,
          notificationsRes
        ] = await Promise.allSettled([
          membersAPI.getAll({ per_page: 1 }),
          loansAPI.getActive({ per_page: 1 }),
          paymentsAPI.getAll({ per_page: 1 }),
          supportTicketsAPI.getAll({ status: 'open', per_page: 1 }),
          paymentNotificationsAPI.getAll({ status: 'pending', per_page: 1 })
        ]);

        setStats({
          members: membersRes.value?.data?.meta?.total || 0,
          activeLoans: loansRes.value?.data?.meta?.total || 0,
          totalPayments: paymentsRes.value?.data?.meta?.total || 0,
          openTickets: ticketsRes.value?.data?.meta?.total || 0,
          pendingNotifications: notificationsRes.value?.data?.meta?.total || 0,
        });
      } else if (isMember() && user?.coopId) {
        // Fetch member dashboard data
        const [eligibilityRes, ticketsRes, notificationsRes] = await Promise.allSettled([
          loanEligibilityAPI.calculate(user.coopId),
          supportTicketsAPI.getAll({ per_page: 1 }),
          paymentNotificationsAPI.getAll({ per_page: 1 })
        ]);

        setMemberData({
          loanEligibility: eligibilityRes.value?.data?.data || null,
          myTickets: ticketsRes.value?.data?.meta?.total || 0,
          myNotifications: notificationsRes.value?.data?.meta?.total || 0,
        });
      }
    } catch (err) {
      console.error("Error fetching dashboard data:", err);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <Page title="Dashboard">
        <div className="transition-content w-full px-[--margin-x] pt-5 lg:pt-6">
          <div className="flex items-center justify-center h-64">
            <div className="text-gray-500">Loading dashboard...</div>
          </div>
        </div>
      </Page>
    );
  }

  return (
    <Page title="Dashboard">
      <div className="transition-content w-full px-[--margin-x] pt-5 lg:pt-6 space-y-6 mb-5">
        {/* Header */}
        <div className="min-w-0">
          <h2 className="truncate text-2xl font-bold tracking-wide text-gray-800 dark:text-dark-50">
            {isAdmin() ? 'Admin Dashboard' : 'My Dashboard'}
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            Welcome back{user?.name ? `, ${user.name}` : ''}!
          </p>
        </div>

        {/* Admin Dashboard */}
        <PermissionGuard requireAdmin>
          {/* Stats Grid */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <StatCard
              title="Total Members"
              value={stats.members}
              icon={UsersIcon}
              color="primary"
            />
            <StatCard
              title="Active Loans"
              value={stats.activeLoans}
              icon={BanknotesIcon}
              color="warning"
            />
            <StatCard
              title="Total Payments"
              value={stats.totalPayments}
              icon={CurrencyDollarIcon}
              color="success"
            />
            <StatCard
              title="Pending Approvals"
              value={stats.pendingNotifications}
              icon={BellAlertIcon}
              color="error"
            />
          </div>

          {/* Additional Stats */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <StatCard
              title="Open Support Tickets"
              value={stats.openTickets}
              icon={LifebuoyIcon}
              color="info"
              subtitle="Requires attention"
            />
            <StatCard
              title="Recent Activity"
              value="Active"
              icon={CheckCircleIcon}
              color="success"
              subtitle="System running smoothly"
            />
          </div>

          {/* Quick Actions */}
          <div>
            <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100 mb-4">
              Quick Actions
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <QuickActionCard
                title="Add Member"
                description="Register a new cooperative member"
                icon={UsersIcon}
                to="/members/create"
                color="primary"
              />
              <QuickActionCard
                title="Approve Payments"
                description="Review pending payment notifications"
                icon={BellAlertIcon}
                to="/payment-notifications"
                color="warning"
              />
              <QuickActionCard
                title="Manage Tickets"
                description="View and respond to support tickets"
                icon={LifebuoyIcon}
                to="/support-tickets"
                color="info"
              />
              <QuickActionCard
                title="Create Loan"
                description="Process a new loan application"
                icon={BanknotesIcon}
                to="/loans/create"
                color="success"
              />
              <QuickActionCard
                title="Capture Payment"
                description="Record a new payment"
                icon={CurrencyDollarIcon}
                to="/payments/create"
                color="success"
              />
              <QuickActionCard
                title="System Settings"
                description="Configure system preferences"
                icon={PlusIcon}
                to="/admin-config/system"
                color="primary"
              />
            </div>
          </div>
        </PermissionGuard>

        {/* Member Dashboard */}
        {isMember() && memberData && (
          <>
            {/* Loan Eligibility Widget */}
            {memberData.loanEligibility && (
              <Card className="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20">
                <div className="flex items-start justify-between">
                  <div>
                    <h3 className="text-lg font-semibold text-gray-900 dark:text-dark-100">
                      Loan Eligibility
                    </h3>
                    <div className="mt-4 space-y-2">
                      <div>
                        <p className="text-sm text-gray-600 dark:text-dark-300">
                          Total Savings
                        </p>
                        <p className="text-xl font-bold text-gray-900 dark:text-dark-100">
                          ₦{Number(memberData.loanEligibility.total_savings || 0).toLocaleString()}
                        </p>
                      </div>
                      <div>
                        <p className="text-sm text-gray-600 dark:text-dark-300">
                          Total Shares
                        </p>
                        <p className="text-xl font-bold text-gray-900 dark:text-dark-100">
                          ₦{Number(memberData.loanEligibility.total_shares || 0).toLocaleString()}
                        </p>
                      </div>
                      <div className="pt-2 border-t border-gray-200 dark:border-dark-500">
                        <p className="text-sm text-gray-600 dark:text-dark-300">
                          Available for New Loan
                        </p>
                        <p className="text-2xl font-bold text-green-600 dark:text-green-400">
                          ₦{Number(memberData.loanEligibility.available_amount || 0).toLocaleString()}
                        </p>
                      </div>
                      {memberData.loanEligibility.active_loan_balance > 0 && (
                        <div>
                          <p className="text-sm text-gray-600 dark:text-dark-300">
                            Active Loan Balance
                          </p>
                          <p className="text-lg font-semibold text-orange-600 dark:text-orange-400">
                            ₦{Number(memberData.loanEligibility.active_loan_balance).toLocaleString()}
                          </p>
                        </div>
                      )}
                    </div>
                  </div>
                  <BanknotesIcon className="size-12 text-blue-500" />
                </div>
              </Card>
            )}

            {/* Member Stats */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <StatCard
                title="My Support Tickets"
                value={memberData.myTickets}
                icon={LifebuoyIcon}
                color="info"
              />
              <StatCard
                title="Payment Notifications"
                value={memberData.myNotifications}
                icon={BellAlertIcon}
                color="warning"
              />
              <StatCard
                title="Account Status"
                value="Active"
                icon={CheckCircleIcon}
                color="success"
              />
            </div>

            {/* Member Quick Actions */}
            <div>
              <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100 mb-4">
                Quick Actions
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <QuickActionCard
                  title="Submit Payment Notification"
                  description="Notify admin about your payment"
                  icon={BellAlertIcon}
                  to="/payment-notifications/create"
                  color="primary"
                />
                <QuickActionCard
                  title="Create Support Ticket"
                  description="Get help from admin"
                  icon={LifebuoyIcon}
                  to="/support-tickets/create"
                  color="info"
                />
                <QuickActionCard
                  title="View My Records"
                  description="Check your payment history"
                  icon={ClockIcon}
                  to="/account"
                  color="success"
                />
              </div>
            </div>
          </>
        )}
      </div>
    </Page>
  );
}
