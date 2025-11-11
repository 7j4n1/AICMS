// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { PlusIcon, MagnifyingGlassIcon, PencilIcon, TrashIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { adminsAPI } from "services/api";

// ----------------------------------------------------------------------

export default function AdminsList() {
  const [admins, setAdmins] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const [pagination, setPagination] = useState({});



  const fetchAdmins = useCallback(async () => {
    try {
      setLoading(true);
      const response = await adminsAPI.getAll({
        search,
        page: currentPage,
        per_page: 15,
      });
      
      const data = response.data?.data || [];
      const metadata = response.data?.meta || response.data?.data?.pagination || {};

      setAdmins(data);
      setPagination(metadata);
    } catch (error) {
      console.error("Error fetching admins:", error);
      toast.error("Failed to load admins");
    } finally {
      setLoading(false);
    }
  }, [search, currentPage]);

  useEffect(() => {
    fetchAdmins();
  }, [fetchAdmins]);

  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this admin?")) {
      return;
    }

    try {
      await adminsAPI.delete(id);
      toast.success("Admin deleted successfully");
      fetchAdmins();
    } catch (error) {
      console.error("Error deleting admin:", error);
      toast.error("Failed to delete admin");
    }
  };

  return (
    <Page title="Admins">
      <div className="space-y-5 mx-4 my-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
              Admins Management
            </h2>
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
              Manage system administrators
            </p>
          </div>
          <Link to="/admins/create">
            <Button color="primary" className="gap-2">
              <PlusIcon className="size-5" />
              Add Admin
            </Button>
          </Link>
        </div>

        {/* Search */}
        <Card className="p-5">
          <Input
            placeholder="Search by name, username, or email..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            prefix={
              <MagnifyingGlassIcon className="size-5 text-gray-400" />
            }
          />
        </Card>

        {/* Admins Table */}
        <Card className="overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50 dark:bg-dark-600">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Name
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Username
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Email
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Coop ID
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Role
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200 bg-white dark:divide-dark-500 dark:bg-dark-700">
                {loading ? (
                  <tr>
                    <td colSpan="6" className="px-6 py-4 text-center">
                      Loading...
                    </td>
                  </tr>
                ) : admins.length === 0 ? (
                  <tr>
                    <td colSpan="6" className="px-6 py-4 text-center text-gray-500">
                      No admins found
                    </td>
                  </tr>
                ) : (
                  admins.map((admin) => (
                    <tr key={admin.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                      <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                        {admin.name}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {admin.username}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {admin.email}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {admin.coopId || "N/A"}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4">
                        <span
                          className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
                            admin.role === "superadmin"
                              ? "bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200"
                              : admin.role === "admin"
                              ? "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                              : "bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300"
                          }`}
                        >
                          {admin.roles && admin.roles.length > 0 ? admin.roles.join(", ") : "N/A"}
                        </span>
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                        <div className="flex items-center justify-end gap-2">
                          <Link to={`/admins/${admin.id}/edit`}>
                            <Button size="sm" variant="outlined" className="gap-1">
                              <PencilIcon className="size-4" />
                              Edit
                            </Button>
                          </Link>
                          <Button
                            size="sm"
                            variant="outlined"
                            color="error"
                            className="gap-1"
                            onClick={() => handleDelete(admin.id)}
                          >
                            <TrashIcon className="size-4" />
                            Delete
                          </Button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
         {pagination.total > 0 && (
           <div className="flex items-center justify-between border-t border-gray-200 bg-white px-6 py-3 dark:border-dark-500 dark:bg-dark-700">
             <div className="text-sm text-gray-500 dark:text-dark-300">
               Showing {((currentPage - 1) * pagination.per_page) + 1} to{" "}
               {Math.min(currentPage * pagination.per_page, pagination.total)} of{" "}
               {pagination.total} results
             </div>
             <div className="flex gap-2">
               <Button
                 size="sm"
                 variant="outlined"
                 disabled={currentPage === 1}
                 onClick={() => setCurrentPage(currentPage - 1)}
               >
                 Previous
               </Button>
               <Button
                 size="sm"
                 variant="outlined"
                 disabled={(currentPage * pagination.per_page) >= pagination.total}
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
