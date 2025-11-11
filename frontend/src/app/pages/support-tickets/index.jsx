// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link, useNavigate } from "react-router";
import { 
  PlusIcon, 
  MagnifyingGlassIcon, 
  EyeIcon
} from "@heroicons/react/24/outline";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input, Badge } from "components/ui";
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

export default function SupportTicketsList() {
  const [tickets, setTickets] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("");
  const [priorityFilter, setPriorityFilter] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [pagination, setPagination] = useState({});
  const navigate = useNavigate();

  const fetchTickets = useCallback(async () => {
    try {
      setLoading(true);
      const response = await supportTicketsAPI.getAll({
        search,
        status: statusFilter,
        priority: priorityFilter,
        page: currentPage,
        per_page: 15,
      });

      const data = response.data?.data || [];
      const meta = response.data?.meta || {};

      setTickets(data);
      setPagination(meta);
    } catch (error) {
      console.error("Error fetching tickets:", error);
      toast.error("Failed to load support tickets");
    } finally {
      setLoading(false);
    }
  }, [search, statusFilter, priorityFilter, currentPage]);

  useEffect(() => {
    fetchTickets();
  }, [fetchTickets]);

  const handleViewTicket = (id) => {
    navigate(`/support-tickets/${id}`);
  };

  return (
    <Page title="Support Tickets">
      <div className="space-y-5 mx-4 my-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
              Support Tickets
            </h2>
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
              Manage support tickets and requests
            </p>
          </div>
          <Link to="/support-tickets/create">
            <Button color="primary" className="gap-2">
              <PlusIcon className="size-5" />
              Create Ticket
            </Button>
          </Link>
        </div>

        {/* Filters */}
        <Card className="p-5">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <Input
              placeholder="Search tickets..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              prefix={<MagnifyingGlassIcon className="size-5 text-gray-400" />}
            />
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
            >
              <option value="">All Status</option>
              <option value="open">Open</option>
              <option value="replied">Replied</option>
              <option value="closed">Closed</option>
            </select>
            <select
              value={priorityFilter}
              onChange={(e) => setPriorityFilter(e.target.value)}
              className="px-3 py-2 border border-gray-300 dark:border-dark-500 rounded-lg bg-white dark:bg-dark-700 text-gray-900 dark:text-dark-100"
            >
              <option value="">All Priorities</option>
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
            </select>
          </div>
        </Card>

        {/* Tickets Table */}
        <Card className="overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200 dark:divide-dark-600">
              <thead className="bg-gray-50 dark:bg-dark-800">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    ID
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Subject
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Priority
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Status
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Created
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-dark-300 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white dark:bg-dark-700 divide-y divide-gray-200 dark:divide-dark-600">
                {loading ? (
                  <tr>
                    <td colSpan="6" className="px-6 py-4 text-center text-sm text-gray-500 dark:text-dark-300">
                      Loading...
                    </td>
                  </tr>
                ) : tickets.length === 0 ? (
                  <tr>
                    <td colSpan="6" className="px-6 py-4 text-center text-sm text-gray-500 dark:text-dark-300">
                      No support tickets found
                    </td>
                  </tr>
                ) : (
                  tickets.map((ticket) => (
                    <tr key={ticket.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-dark-100">
                        #{ticket.id}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-900 dark:text-dark-100">
                        <div className="max-w-xs truncate">{ticket.subject}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <Badge color={priorityColors[ticket.priority]}>
                          {ticket.priority}
                        </Badge>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <Badge color={statusColors[ticket.status]}>
                          {ticket.status}
                        </Badge>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-dark-300">
                        {new Date(ticket.created_at).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <Button
                          size="sm"
                          variant="text"
                          onClick={() => handleViewTicket(ticket.id)}
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
