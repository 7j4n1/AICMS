// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Card } from "components/ui";
import { accountAPI } from "services/api";
import { useAuthContext } from "app/contexts/auth/context";

// ----------------------------------------------------------------------

export default function Profile() {
  const { user } = useAuthContext();
  const [balance, setBalance] = useState(null);
  const [savings, setSavings] = useState([]);
  const [shares, setShares] = useState([]);
  const [loading, setLoading] = useState(true);

  const fetchAccountData = useCallback(async () => {
    try {
      setLoading(true);
      
      // Fetch balance
      const balanceResponse = await accountAPI.getBalance();
      setBalance(balanceResponse.data);

      // Fetch savings
      const savingsResponse = await accountAPI.getSavings();
      setSavings(savingsResponse.data?.data || []);

      // Fetch shares
      const sharesResponse = await accountAPI.getShares();
      setShares(sharesResponse.data?.data || []);
    } catch (error) {
      console.error("Error fetching account data:", error);
      toast.error("Failed to load account information");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchAccountData();
  }, [fetchAccountData]);

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-NG", {
      style: "currency",
      currency: "NGN",
    }).format(amount || 0);
  };

  return (
    <Page title="My Profile">
      <div className="space-y-5">
        {/* Header */}
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            My Profile
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            View your account information and balances
          </p>
        </div>

        {/* Profile Information */}
        <Card className="p-6">
          <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
            Profile Information
          </h3>
          <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
            <InfoRow label="Name" value={user?.name} />
            <InfoRow label="Username" value={user?.username} />
            <InfoRow label="Email" value={user?.email} />
            <InfoRow label="Coop ID" value={user?.coopId} />
          </div>
        </Card>

        {/* Account Balance */}
        {!loading && balance && (
          <Card className="p-6">
            <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
              Account Balance
            </h3>
            <div className="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
              <BalanceCard
                label="Savings"
                amount={balance.savings}
                color="blue"
              />
              <BalanceCard
                label="Shares"
                amount={balance.shares}
                color="green"
              />
              <BalanceCard
                label="Loan"
                amount={balance.loan}
                color={balance.loan < 0 ? "red" : "gray"}
              />
              <BalanceCard
                label="Total Balance"
                amount={balance.total_balance}
                color="purple"
              />
            </div>
          </Card>
        )}

        {/* Recent Transactions */}
        <div className="grid gap-5 md:grid-cols-2">
          {/* Savings Records */}
          <Card className="p-6">
            <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
              Recent Savings
            </h3>
            {loading ? (
              <p className="text-center text-gray-500">Loading...</p>
            ) : savings.length === 0 ? (
              <p className="text-center text-gray-500">No savings records</p>
            ) : (
              <div className="space-y-2">
                {savings.slice(0, 5).map((saving) => (
                  <div
                    key={saving.id}
                    className="flex justify-between border-b border-gray-100 pb-2 dark:border-dark-500"
                  >
                    <span className="text-sm text-gray-600 dark:text-dark-300">
                      {new Date(saving.paymentDate).toLocaleDateString()}
                    </span>
                    <span className="text-sm font-semibold text-gray-900 dark:text-dark-100">
                      {formatCurrency(saving.savingsAmount)}
                    </span>
                  </div>
                ))}
              </div>
            )}
          </Card>

          {/* Shares Records */}
          <Card className="p-6">
            <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
              Recent Shares
            </h3>
            {loading ? (
              <p className="text-center text-gray-500">Loading...</p>
            ) : shares.length === 0 ? (
              <p className="text-center text-gray-500">No shares records</p>
            ) : (
              <div className="space-y-2">
                {shares.slice(0, 5).map((share) => (
                  <div
                    key={share.id}
                    className="flex justify-between border-b border-gray-100 pb-2 dark:border-dark-500"
                  >
                    <span className="text-sm text-gray-600 dark:text-dark-300">
                      {new Date(share.paymentDate).toLocaleDateString()}
                    </span>
                    <span className="text-sm font-semibold text-gray-900 dark:text-dark-100">
                      {formatCurrency(share.shareAmount)}
                    </span>
                  </div>
                ))}
              </div>
            )}
          </Card>
        </div>
      </div>
    </Page>
  );
}

function InfoRow({ label, value }) {
  return (
    <div className="flex justify-between border-b border-gray-100 pb-2 dark:border-dark-500">
      <span className="text-sm font-medium text-gray-600 dark:text-dark-300">
        {label}:
      </span>
      <span className="text-sm text-gray-900 dark:text-dark-100">
        {value || "N/A"}
      </span>
    </div>
  );
}

function BalanceCard({ label, amount, color }) {
  const colorClasses = {
    blue: "bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400",
    green: "bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400",
    red: "bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400",
    purple: "bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400",
    gray: "bg-gray-50 dark:bg-gray-900/20 text-gray-700 dark:text-gray-400",
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-NG", {
      style: "currency",
      currency: "NGN",
    }).format(amount || 0);
  };

  return (
    <div className={`rounded-lg p-4 ${colorClasses[color]}`}>
      <p className="mb-1 text-xs font-medium opacity-80">{label}</p>
      <p className="text-xl font-bold">{formatCurrency(amount)}</p>
    </div>
  );
}
