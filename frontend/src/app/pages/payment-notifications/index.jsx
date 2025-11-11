// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link, useNavigate } from "react-router";
import { 
  PlusIcon, 
  EyeIcon
} from "@heroicons/react/24/outline";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Badge } from "components/ui";
import { paymentNotificationsAPI } from "services/api";

// ----------------------------------------------------------------------

const statusColors = {
  pending: "warning",
  approved: "success",
  rejected: "error",
};

export default function PaymentNotificationsList() {
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [statusFilter, setStatusFilter] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [pagination, setPagination] = useState({});
  const navigate = useNavigate();

  const fetchNotifications = useCallback(async () => {
    try {
      setLoading(true);
      const response = await paymentNotificationsAPI.getAll({
        status: statusFilter,
        page: currentPage,
        per_page: 15,
      });

      const data = response.data?.data || [];
      const meta = response.data?.meta || {};

      setNotifications(data);
      setPagination(meta);
    } catch (error) {
      console.error("Error fetching notifications:", error);
      toast.error("Failed to load payment notifications");
    } finally {
      setLoading(false);
    }
  }, [statusFilter, currentPage]);

  useEffect(() => {
    fetchNotifications();
  }, [fetchNotifications]);

  const handleViewNotification = (id) => {
    navigate(`/payment-notifications/${id}`);
  };

  return (
    <Page title="Payment Notifications">
      <div className="space-y-5 mx-4 my-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
              Payment Notifications
            </h2>
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
              Submit and track payment notifications
            </p>
          </div>
          <Link to="/payment-notifications/create">
            <Button color="primary" className="gap-2">
              <PlusIcon className="size-5" />
              Submit Notification
            </Button>
          </Link>
        </div>

        {/* Filters */}
        <Card className="p-5">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
        </Card>

        {/* Notifications Table */}
        <Card className="overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200 dark:divide-dark-600">
              <thead className="bg-gray-50 dark:bg-dark-800">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    ID
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Amount
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Payment Date
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Bank
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Status
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Submitted
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white dark:bg-dark-700 divide-y divide-gray-200 dark:divide-dark-600">
                {loading ? (
                  <tr>
                    <td colSpan="7" className="px-6 py-4 text-center text-sm text-gray-500 dark:text-dark-300">
                      Loading...
                    </td>
                  </tr>
                ) : notifications.length === 0 ? (
                  <tr>
                    <td colSpan="7" className="px-6 py-4 text-center text-sm text-gray-500 dark:text-dark-300">
                      No payment notifications found
                    </td>
                  </tr>
                ) : (
                  notifications.map((notification) => (
                    <tr key={notification.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-dark-100">
                        #{notification.id}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-dark-100">
                        ₦{Number(notification.amount).toLocaleString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-dark-300">
                        {new Date(notification.payment_date).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-900 dark:text-dark-100">
                        {notification.bank_used}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <Badge color={statusColors[notification.status]}>
                          {notification.status}
                        </Badge>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-dark-300">
                        {new Date(notification.created_at).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Button
                          size="sm"
                          variant="text"
                          onClick={() => handleViewNotification(notification.id)}
                          className="gap-2"
                        >
                          <EyeIcon className="size-4" />
                          View
                        </Button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {pagination.total > 0 && (
            <div className="px-6 py-4 flex items-center justify-between border-t border-gray-200 dark:border-dark-600">
              <div className="text-sm text-gray-700 dark:text-dark-300">
                Showing {((currentPage - 1) * pagination.per_page) + 1} to{" "}
                {Math.min(currentPage * pagination.per_page, pagination.total)} of{" "}
                {pagination.total} results
              </div>
              <div className="flex gap-2">
                <Button
                  size="sm"
                  disabled={currentPage === 1}
                  onClick={() => setCurrentPage(currentPage - 1)}
                >
                  Previous
                </Button>
                <Button
                  size="sm"
                  disabled={currentPage === pagination.last_page}
                  onClick={() => setCurrentPage(currentPage + 1)}
                >
                  Next
                </Button>
              </div>
            </div>
          )}
        </Card>
      </div>
    </Page>
  );
}
