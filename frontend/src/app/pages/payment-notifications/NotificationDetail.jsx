// Import Dependencies
import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router";
import { toast } from "sonner";
import {
  ArrowLeftIcon,
  CheckCircleIcon,
  XCircleIcon,
  DocumentIcon,
} from "@heroicons/react/24/outline";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Badge } from "components/ui";
import { PermissionGuard } from "components/shared/PermissionGuard";
import { paymentNotificationsAPI } from "services/api";

// ----------------------------------------------------------------------

const statusColors = {
  pending: "warning",
  approved: "success",
  rejected: "error",
};

export default function NotificationDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [notification, setNotification] = useState(null);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);

  useEffect(() => {
    fetchNotification();
  }, [id]);

  const fetchNotification = async () => {
    try {
      setLoading(true);
      const response = await paymentNotificationsAPI.getById(id);
      setNotification(response.data?.data);
    } catch (err) {
      console.error("Error fetching notification:", err);
      toast.error("Failed to load notification details");
    } finally {
      setLoading(false);
    }
  };

  const handleApprove = async () => {
    if (!window.confirm("Are you sure you want to approve this payment notification?")) {
      return;
    }

    try {
      setActionLoading(true);
      await paymentNotificationsAPI.approve(id);
      toast.success("Payment notification approved successfully");
      fetchNotification();
    } catch (error) {
      console.error("Error approving notification:", error);
      toast.error("Failed to approve notification");
    } finally {
      setActionLoading(false);
    }
  };

  const handleReject = async () => {
    const reason = window.prompt("Please enter rejection reason:");
    if (!reason) {
      return;
    }

    try {
      setActionLoading(true);
      await paymentNotificationsAPI.reject(id, reason);
      toast.success("Payment notification rejected successfully");
      fetchNotification();
    } catch (error) {
      console.error("Error rejecting notification:", error);
      toast.error("Failed to reject notification");
    } finally {
      setActionLoading(false);
    }
  };

  if (loading) {
    return (
      <Page title="Notification Details">
        <div className="flex items-center justify-center h-64">
          <div className="text-gray-500">Loading...</div>
        </div>
      </Page>
    );
  }

  if (!notification) {
    return (
      <Page title="Notification Details">
        <div className="flex items-center justify-center h-64">
          <div className="text-gray-500">Notification not found</div>
        </div>
      </Page>
    );
  }

  return (
    <Page title={`Notification #${notification.id}`}>
      <div className="max-w-4xl mx-auto space-y-5 my-6 px-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-4">
            <Button
              variant="text"
              onClick={() => navigate("/payment-notifications")}
              className="gap-2"
            >
              <ArrowLeftIcon className="size-5" />
              Back
            </Button>
            <div>
              <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
                Payment Notification #{notification.id}
              </h2>
              <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
                Submitted on {new Date(notification.created_at).toLocaleString()}
              </p>
            </div>
          </div>
          <PermissionGuard requireAdmin>
            {notification.status === "pending" && (
              <div className="flex gap-2">
                <Button
                  color="success"
                  onClick={handleApprove}
                  disabled={actionLoading}
                  className="gap-2"
                >
                  <CheckCircleIcon className="size-5" />
                  Approve
                </Button>
                <Button
                  color="error"
                  onClick={handleReject}
                  disabled={actionLoading}
                  className="gap-2"
                >
                  <XCircleIcon className="size-5" />
                  Reject
                </Button>
              </div>
            )}
          </PermissionGuard>
        </div>

        {/* Status Badge */}
        <div>
          <Badge color={statusColors[notification.status]} className="text-base px-4 py-2">
            {notification.status.toUpperCase()}
          </Badge>
        </div>

        {/* Payment Details */}
        <Card className="p-6">
          <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100 mb-4">
            Payment Details
          </h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Amount</div>
              <div className="mt-1 text-lg font-semibold text-gray-900 dark:text-dark-100">
                ₦{Number(notification.amount).toLocaleString()}
              </div>
            </div>
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Payment Date</div>
              <div className="mt-1 font-medium text-gray-900 dark:text-dark-100">
                {new Date(notification.payment_date).toLocaleDateString()}
              </div>
            </div>
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Payment Time</div>
              <div className="mt-1 font-medium text-gray-900 dark:text-dark-100">
                {notification.payment_time}
              </div>
            </div>
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Bank Used</div>
              <div className="mt-1 font-medium text-gray-900 dark:text-dark-100">
                {notification.bank_used}
              </div>
            </div>
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Payment Channel</div>
              <div className="mt-1 font-medium text-gray-900 dark:text-dark-100">
                {notification.payment_channel}
              </div>
            </div>
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Depositor Name</div>
              <div className="mt-1 font-medium text-gray-900 dark:text-dark-100">
                {notification.depositor_name}
              </div>
            </div>
            {notification.reference_number && (
              <div>
                <div className="text-sm text-gray-500 dark:text-dark-400">Reference Number</div>
                <div className="mt-1 font-medium text-gray-900 dark:text-dark-100">
                  {notification.reference_number}
                </div>
              </div>
            )}
          </div>

          {notification.additional_details && (
            <div className="mt-6">
              <div className="text-sm text-gray-500 dark:text-dark-400">Additional Details</div>
              <div className="mt-1 text-gray-900 dark:text-dark-100">
                {notification.additional_details}
              </div>
            </div>
          )}
        </Card>

        {/* Evidence */}
        {notification.evidence_path && (
          <Card className="p-6">
            <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100 mb-4">
              Payment Evidence
            </h3>
            <a
              href={`/storage/${notification.evidence_path.replace('public/', '')}`}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30"
            >
              <DocumentIcon className="size-5" />
              View Evidence
            </a>
          </Card>
        )}

        {/* Approval/Rejection Info */}
        {notification.status === "approved" && (
          <Card className="p-6 bg-green-50 dark:bg-green-900/20">
            <h3 className="text-lg font-semibold text-green-800 dark:text-green-100 mb-2">
              Approved
            </h3>
            <div className="text-sm text-green-700 dark:text-green-200">
              Approved by: <span className="font-medium">{notification.approved_by}</span>
            </div>
            <div className="text-sm text-green-700 dark:text-green-200">
              Approved on: <span className="font-medium">
                {new Date(notification.approved_at).toLocaleString()}
              </span>
            </div>
          </Card>
        )}

        {notification.status === "rejected" && (
          <Card className="p-6 bg-red-50 dark:bg-red-900/20">
            <h3 className="text-lg font-semibold text-red-800 dark:text-red-100 mb-2">
              Rejected
            </h3>
            <div className="text-sm text-red-700 dark:text-red-200 mb-2">
              Rejected by: <span className="font-medium">{notification.rejected_by}</span>
            </div>
            <div className="text-sm text-red-700 dark:text-red-200 mb-2">
              Rejected on: <span className="font-medium">
                {new Date(notification.rejected_at).toLocaleString()}
              </span>
            </div>
            <div className="mt-4">
              <div className="text-sm font-medium text-red-800 dark:text-red-100">Reason:</div>
              <div className="mt-1 text-sm text-red-700 dark:text-red-200">
                {notification.rejected_reason}
              </div>
            </div>
          </Card>
        )}
      </div>
    </Page>
  );
}
