// Import Dependencies
import { useState } from "react";
import { useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { PaperClipIcon, XMarkIcon } from "@heroicons/react/24/outline";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { supportTicketsAPI } from "services/api";

// ----------------------------------------------------------------------

export default function CreateTicket() {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [attachments, setAttachments] = useState([]);

  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm();

  const handleFileChange = (e) => {
    const files = Array.from(e.target.files);
    setAttachments([...attachments, ...files]);
  };

  const removeFile = (index) => {
    setAttachments(attachments.filter((_, i) => i !== index));
  };

  const onSubmit = async (data) => {
    try {
      setLoading(true);

      const formData = new FormData();
      formData.append("subject", data.subject);
      formData.append("priority", data.priority);
      formData.append("message", data.message);

      // Append attachments
      attachments.forEach((file) => {
        formData.append("attachments[]", file);
      });

      await supportTicketsAPI.create(formData);

      toast.success("Support ticket created successfully");
      navigate("/support-tickets");
    } catch (error) {
      console.error("Error creating ticket:", error);
      toast.error(error.response?.data?.message || "Failed to create ticket");
    } finally {
      setLoading(false);
    }
  };

  return (
    <Page title="Create Support Ticket">
      <div className="max-w-3xl mx-auto space-y-5 my-6 px-4">
        {/* Header */}
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            Create Support Ticket
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            Submit a new support request
          </p>
        </div>

        {/* Form */}
        <form onSubmit={handleSubmit(onSubmit)}>
          <Card className="p-6 space-y-6">
            {/* Subject */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Subject <span className="text-red-500">*</span>
              </label>
              <Input
                {...register("subject", { required: "Subject is required" })}
                placeholder="Enter ticket subject"
                error={errors.subject?.message}
              />
            </div>

            {/* Priority */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Priority <span className="text-red-500">*</span>
              </label>
              <select
                {...register("priority", { required: "Priority is required" })}
                className="w-full px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
              >
                <option value="">Select priority</option>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
              {errors.priority && (
                <p className="mt-1 text-sm text-red-600">{errors.priority.message}</p>
              )}
            </div>

            {/* Message */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Message <span className="text-red-500">*</span>
              </label>
              <textarea
                {...register("message", { required: "Message is required" })}
                rows={6}
                placeholder="Describe your issue or request..."
                className="w-full px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
              />
              {errors.message && (
                <p className="mt-1 text-sm text-red-600">{errors.message.message}</p>
              )}
            </div>

            {/* Attachments */}
            <div>
              <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                Attachments (Optional)
              </label>
              <div className="mt-2">
                <label className="cursor-pointer inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-dark-500 rounded-lg hover:bg-gray-50 dark:hover:bg-dark-600">
                  <PaperClipIcon className="size-5" />
                  <span className="text-sm">Choose files</span>
                  <input
                    type="file"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                    onChange={handleFileChange}
                    className="hidden"
                  />
                </label>
                <p className="mt-1 text-xs text-gray-500 dark:text-dark-400">
                  Supported formats: JPG, PNG, PDF, DOC, DOCX (Max 5MB each)
                </p>
              </div>

              {/* File List */}
              {attachments.length > 0 && (
                <div className="mt-4 space-y-2">
                  {attachments.map((file, index) => (
                    <div
                      key={index}
                      className="flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-600 rounded-lg"
                    >
                      <div className="flex items-center gap-2">
                        <PaperClipIcon className="size-5 text-gray-400" />
                        <span className="text-sm text-gray-700 dark:text-dark-200">
                          {file.name}
                        </span>
                        <span className="text-xs text-gray-500 dark:text-dark-400">
                          ({(file.size / 1024).toFixed(2)} KB)
                        </span>
                      </div>
                      <button
                        type="button"
                        onClick={() => removeFile(index)}
                        className="text-red-600 hover:text-red-800"
                      >
                        <XMarkIcon className="size-5" />
                      </button>
                    </div>
                  ))}
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
                {loading ? "Creating..." : "Create Ticket"}
              </Button>
              <Button
                type="button"
                variant="outlined"
                onClick={() => navigate("/support-tickets")}
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
