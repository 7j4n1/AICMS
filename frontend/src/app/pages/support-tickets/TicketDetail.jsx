// Import Dependencies
import { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import {
  ArrowLeftIcon,
  PaperClipIcon,
  XMarkIcon,
  PaperAirplaneIcon,
} from "@heroicons/react/24/outline";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Badge } from "components/ui";
import { PermissionGuard } from "components/shared/PermissionGuard";
import { supportTicketsAPI } from "services/api";

// ----------------------------------------------------------------------

const priorityColors = {
  low: "success",
  medium: "warning",
  high: "error",
};

const statusColors = {
  open: "info",
  replied: "warning",
  closed: "default",
};

export default function TicketDetail() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [ticket, setTicket] = useState(null);
  const [loading, setLoading] = useState(true);
  const [sending, setSending] = useState(false);
  const [attachments, setAttachments] = useState([]);

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm();

  useEffect(() => {
    fetchTicket();
  }, [id]);

  const fetchTicket = async () => {
    try {
      setLoading(true);
      const response = await supportTicketsAPI.getById(id);
      setTicket(response.data?.data);
    } catch (error) {
      console.error("Error fetching ticket:", error);
      toast.error("Failed to load ticket details");
    } finally {
      setLoading(false);
    }
  };

  const handleFileChange = (e) => {
    const files = Array.from(e.target.files);
    setAttachments([...attachments, ...files]);
  };

  const removeFile = (index) => {
    setAttachments(attachments.filter((_, i) => i !== index));
  };

  const onSubmitReply = async (data) => {
    try {
      setSending(true);

      const formData = new FormData();
      formData.append("message", data.message);

      attachments.forEach((file) => {
        formData.append("attachments[]", file);
      });

      await supportTicketsAPI.addMessage(id, formData);

      toast.success("Reply sent successfully");
      reset();
      setAttachments([]);
      fetchTicket();
    } catch (error) {
      console.error("Error sending reply:", error);
      toast.error("Failed to send reply");
    } finally {
      setSending(false);
    }
  };

  const handleCloseTicket = async () => {
    if (!window.confirm("Are you sure you want to close this ticket?")) {
      return;
    }

    try {
      await supportTicketsAPI.update(id, { status: "closed" });
      toast.success("Ticket closed successfully");
      fetchTicket();
    } catch (error) {
      console.error("Error closing ticket:", error);
      toast.error("Failed to close ticket");
    }
  };

  if (loading) {
    return (
      <Page title="Ticket Details">
        <div className="flex items-center justify-center h-64">
          <div className="text-gray-500">Loading...</div>
        </div>
      </Page>
    );
  }

  if (!ticket) {
    return (
      <Page title="Ticket Details">
        <div className="flex items-center justify-center h-64">
          <div className="text-gray-500">Ticket not found</div>
        </div>
      </Page>
    );
  }

  return (
    <Page title={`Ticket #${ticket.id}`}>
      <div className="max-w-4xl mx-auto space-y-5 my-6 px-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-4">
            <Button
              variant="text"
              onClick={() => navigate("/support-tickets")}
              className="gap-2"
            >
              <ArrowLeftIcon className="size-5" />
              Back
            </Button>
            <div>
              <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
                Ticket #{ticket.id}
              </h2>
              <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
                {ticket.subject}
              </p>
            </div>
          </div>
          <PermissionGuard requireAdmin>
            {ticket.status !== "closed" && (
              <Button color="error" onClick={handleCloseTicket}>
                Close Ticket
              </Button>
            )}
          </PermissionGuard>
        </div>

        {/* Ticket Info */}
        <Card className="p-6">
          <div className="flex gap-6 flex-wrap">
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Priority</div>
              <Badge color={priorityColors[ticket.priority]} className="mt-1">
                {ticket.priority}
              </Badge>
            </div>
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Status</div>
              <Badge color={statusColors[ticket.status]} className="mt-1">
                {ticket.status}
              </Badge>
            </div>
            <div>
              <div className="text-sm text-gray-500 dark:text-dark-400">Created</div>
              <div className="mt-1 text-sm font-medium text-gray-900 dark:text-dark-100">
                {new Date(ticket.created_at).toLocaleString()}
              </div>
            </div>
          </div>
        </Card>

        {/* Initial Message */}
        <Card className="p-6">
          <div className="flex items-start gap-4">
            <div className="flex-shrink-0">
              <div className="size-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                {ticket.member?.surname?.[0] || "M"}
              </div>
            </div>
            <div className="flex-1">
              <div className="flex items-center gap-2">
                <span className="font-semibold text-gray-900 dark:text-dark-100">
                  {ticket.member?.surname} {ticket.member?.otherNames}
                </span>
                <span className="text-sm text-gray-500 dark:text-dark-400">
                  {new Date(ticket.created_at).toLocaleString()}
                </span>
              </div>
              <div className="mt-2 text-gray-700 dark:text-dark-200 whitespace-pre-wrap">
                {ticket.message}
              </div>
              {ticket.attachments && ticket.attachments.length > 0 && (
                <div className="mt-4 flex flex-wrap gap-2">
                  {ticket.attachments.map((attachment, index) => (
                    <a
                      key={index}
                      href={`/storage/${attachment}`}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-dark-600 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-dark-500"
                    >
                      <PaperClipIcon className="size-4" />
                      Attachment {index + 1}
                    </a>
                  ))}
                </div>
              )}
            </div>
          </div>
        </Card>

        {/* Messages */}
        {ticket.messages && ticket.messages.length > 0 && (
          <div className="space-y-4">
            <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100">
              Replies
            </h3>
            {ticket.messages.map((message) => (
              <Card key={message.id} className="p-6">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <div
                      className={`size-10 rounded-full flex items-center justify-center text-white font-semibold ${
                        message.sender_type === "admin"
                          ? "bg-green-500"
                          : "bg-blue-500"
                      }`}
                    >
                      {message.sender_type === "admin" ? "A" : "M"}
                    </div>
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center gap-2">
                      <span className="font-semibold text-gray-900 dark:text-dark-100">
                        {message.sender_type === "admin" ? "Admin" : "Member"}
                      </span>
                      <span className="text-sm text-gray-500 dark:text-dark-400">
                        {new Date(message.created_at).toLocaleString()}
                      </span>
                    </div>
                    <div className="mt-2 text-gray-700 dark:text-dark-200 whitespace-pre-wrap">
                      {message.message}
                    </div>
                    {message.attachments && message.attachments.length > 0 && (
                      <div className="mt-4 flex flex-wrap gap-2">
                        {message.attachments.map((attachment, index) => (
                          <a
                            key={index}
                            href={`/storage/${attachment}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-dark-600 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-dark-500"
                          >
                            <PaperClipIcon className="size-4" />
                            Attachment {index + 1}
                          </a>
                        ))}
                      </div>
                    )}
                  </div>
                </div>
              </Card>
            ))}
          </div>
        )}

        {/* Reply Form */}
        {ticket.status !== "closed" && (
          <form onSubmit={handleSubmit(onSubmitReply)}>
            <Card className="p-6 space-y-4">
              <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100">
                Add Reply
              </h3>

              <div>
                <textarea
                  {...register("message", { required: "Message is required" })}
                  rows={4}
                  placeholder="Type your reply..."
                  className="w-full px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
                />
                {errors.message && (
                  <p className="mt-1 text-sm text-red-600">{errors.message.message}</p>
                )}
              </div>

              <div>
                <label className="cursor-pointer inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-dark-500 rounded-lg hover:bg-gray-50 dark:hover:bg-dark-600">
                  <PaperClipIcon className="size-5" />
                  <span className="text-sm">Attach files</span>
                  <input
                    type="file"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                    onChange={handleFileChange}
                    className="hidden"
                  />
                </label>
              </div>

              {attachments.length > 0 && (
                <div className="space-y-2">
                  {attachments.map((file, index) => (
                    <div
                      key={index}
                      className="flex items-center justify-between p-3 bg-gray-50 dark:bg-dark-600 rounded-lg"
                    >
                      <div className="flex items-center gap-2">
                        <PaperClipIcon className="size-5 text-gray-400" />
                        <span className="text-sm">{file.name}</span>
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

              <Button
                type="submit"
                color="primary"
                disabled={sending}
                className="gap-2"
              >
                <PaperAirplaneIcon className="size-5" />
                {sending ? "Sending..." : "Send Reply"}
              </Button>
            </Card>
          </form>
        )}
      </div>
    </Page>
  );
}
