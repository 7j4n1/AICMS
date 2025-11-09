// Import Dependencies
import { useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { PaperClipIcon, XMarkIcon } from "@heroicons/react/24/outline";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { paymentNotificationsAPI } from "services/api";

// ----------------------------------------------------------------------

export default function CreateNotification() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [evidenceFile, setEvidenceFile] = useState(null);

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm();

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      if (file.size > 2 * 1024 * 1024) {
        toast.error("File size must be less than 2MB");
        return;
      }
      setEvidenceFile(file);
    }
  };

  const onSubmit = async (data) => {
    if (!evidenceFile) {
      toast.error("Please upload payment evidence");
      return;
    }

    try {
      setLoading(true);

      const formData = new FormData();
      formData.append("amount", data.amount);
      formData.append("payment_date", data.payment_date);
      formData.append("payment_time", data.payment_time);
      formData.append("bank_used", data.bank_used);
      formData.append("payment_channel", data.payment_channel);
      formData.append("depositor_name", data.depositor_name);
      formData.append("reference_number", data.reference_number || "");
      formData.append("additional_details", data.additional_details || "");
      formData.append("evidence", evidenceFile);

      await paymentNotificationsAPI.create(formData);

      toast.success("Payment notification submitted successfully");
      navigate("/payment-notifications");
    } catch (error) {
      console.error("Error creating notification:", error);
      toast.error(error.response?.data?.message || "Failed to submit notification");
    } finally {
      setLoading(false);
    }
  };

  return (
    <Page title="Submit Payment Notification">
      <div className="max-w-3xl mx-auto space-y-5 my-6 px-4">
        {/* Header */}
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            Submit Payment Notification
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            Notify admin about your payment
          </p>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit(onSubmit)}>
          <Card className="p-6 space-y-6">
            {/* Amount */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Amount (₦) <span className="text-red-500">*</span>
              </label>
              <Input
                type="number"
                step="0.01"
                {...register("amount", { 
                  required: "Amount is required",
                  min: { value: 1, message: "Amount must be greater than 0" }
                })}
                placeholder="Enter amount"
                error={errors.amount?.message}
              />
            </div>

            {/* Payment Date & Time */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Payment Date <span className="text-red-500">*</span>
                </label>
                <Input
                  type="date"
                  {...register("payment_date", { required: "Payment date is required" })}
                  error={errors.payment_date?.message}
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Payment Time <span className="text-red-500">*</span>
                </label>
                <Input
                  type="time"
                  {...register("payment_time", { required: "Payment time is required" })}
                  error={errors.payment_time?.message}
                />
              </div>
            </div>

            {/* Bank Used */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Bank <span className="text-red-500">*</span>
              </label>
              <Input
                {...register("bank_used", { required: "Bank is required" })}
                placeholder="Enter bank name"
                error={errors.bank_used?.message}
              />
            </div>

            {/* Payment Channel */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Payment Channel <span className="text-red-500">*</span>
              </label>
              <select
                {...register("payment_channel", { required: "Payment channel is required" })}
                className="w-full px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
              >
                <option value="">Select channel</option>
                <option value="Bank deposit">Bank deposit</option>
                <option value="USSD">USSD</option>
                <option value="Internet banking">Internet banking</option>
                <option value="ATM transfer">ATM transfer</option>
                <option value="POS transfer">POS transfer</option>
                <option value="Mobile app">Mobile app</option>
              </select>
              {errors.payment_channel && (
                <p className="mt-1 text-sm text-red-600">{errors.payment_channel.message}</p>
              )}
            </div>

            {/* Depositor Name */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Depositor Name <span className="text-red-500">*</span>
              </label>
              <Input
                {...register("depositor_name", { required: "Depositor name is required" })}
                placeholder="Enter depositor name"
                error={errors.depositor_name?.message}
              />
            </div>

            {/* Reference Number */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Reference Number (Optional)
              </label>
              <Input
                {...register("reference_number")}
                placeholder="Enter transaction reference number"
              />
            </div>

            {/* Additional Details */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Additional Details (Optional)
              </label>
              <textarea
                {...register("additional_details")}
                rows={3}
                placeholder="Any additional information..."
                className="w-full px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
              />
            </div>

            {/* Evidence Upload */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Payment Evidence <span className="text-red-500">*</span>
              </label>
              <div className="mt-2">
                <label className="cursor-pointer inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-dark-500 rounded-lg hover:bg-gray-50 dark:hover:bg-dark-600">
                  <PaperClipIcon className="size-5" />
                  <span className="text-sm">Choose file</span>
                  <input
                    type="file"
                    accept=".jpg,.jpeg,.png,.pdf"
                    onChange={handleFileChange}
                    className="hidden"
                  />
                </label>
                <p className="mt-1 text-xs text-gray-500 dark:text-dark-400">
                  Supported formats: JPG, PNG, PDF (Max 2MB)
                </p>
              </div>

              {evidenceFile && (
                <div className="mt-4 flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-600 rounded-lg">
                  <div className="flex items-center gap-2">
                    <PaperClipIcon className="size-5 text-gray-400" />
                    <span className="text-sm text-gray-700 dark:text-dark-200">
                      {evidenceFile.name}
                    </span>
                    <span className="text-xs text-gray-500 dark:text-dark-400">
                      ({(evidenceFile.size / 1024).toFixed(2)} KB)
                    </span>
                  </div>
                  <button
                    type="button"
                    onClick={() => setEvidenceFile(null)}
                    className="text-red-600 hover:text-red-800"
                  >
                    <XMarkIcon className="size-5" />
                  </button>
                </div>
              )}
            </div>

            {/* Actions */}
            <div className="flex gap-3 pt-4">
              <Button
                type="submit"
                color="primary"
                disabled={loading}
                className="flex-1"
              >
                {loading ? "Submitting..." : "Submit Notification"}
              </Button>
              <Button
                type="button"
                variant="outlined"
                onClick={() => navigate("/payment-notifications")}
                disabled={loading}
              >
                Cancel
              </Button>
            </div>
          </Card>
        </form>
      </div>
    </Page>
  );
}
