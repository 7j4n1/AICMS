// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { PlusIcon, MagnifyingGlassIcon, PencilIcon, TrashIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";
import dayjs from "dayjs";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { itemsAPI } from "services/api";

// ----------------------------------------------------------------------

export default function ItemsList() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");

  const fetchItems = useCallback(async () => {
    try {
      setLoading(true);
      const params = {
        per_page: 25,
      };
      
      if (search) {
        params.coop_id = search;
      }
      
      if (statusFilter !== "all") {
        params.payment_status = statusFilter;
      }

      const response = await itemsAPI.getAll(params);
      const data = response.data?.data || [];
      setItems(data);
    } catch (error) {
      console.error("Error fetching items:", error);
      toast.error("Failed to load items");
    } finally {
      setLoading(false);
    }
  }, [search, statusFilter]);

  useEffect(() => {
    fetchItems();
  }, [fetchItems]);

  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this item?")) {
      return;
    }

    try {
      await itemsAPI.delete(id);
      toast.success("Item deleted successfully");
      fetchItems();
    } catch (error) {
      console.error("Error deleting item:", error);
      toast.error("Failed to delete item");
    }
  };

  const formatCurrency = (amount) => {
    return new Intl.NumberFormat("en-NG", {
      style: "currency",
      currency: "NGN",
    }).format(amount);
  };

  return (
    <Page title="Items">
      <div className="space-y-5">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
              Items Management
            </h2>
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
              Manage business items
            </p>
          </div>
          <Link to="/business/items/create">
            <Button color="primary" className="gap-2">
              <PlusIcon className="size-5" />
              Add Item
            </Button>
          </Link>
        </div>

        {/* Filters */}
        <Card className="p-5">
          <div className="flex gap-3">
            <div className="flex-1">
              <Input
                placeholder="Search by Coop ID..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                prefix={
                  <MagnifyingGlassIcon className="size-5 text-gray-400" />
                }
              />
            </div>
            <select
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
              className="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-dark-500 dark:bg-dark-700"
            >
              <option value="all">All Status</option>
              <option value="0">Unpaid</option>
              <option value="1">Paid</option>
            </select>
          </div>
        </Card>

        {/* Items Table */}
        <Card className="overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50 dark:bg-dark-600">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Coop ID
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Description
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Quantity
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Price
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Total
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Buying Date
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Status
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200 bg-white dark:divide-dark-500 dark:bg-dark-700">
                {loading ? (
                  <tr>
                    <td colSpan="8" className="px-6 py-4 text-center">
                      Loading...
                    </td>
                  </tr>
                ) : items.length === 0 ? (
                  <tr>
                    <td colSpan="8" className="px-6 py-4 text-center text-gray-500">
                      No items found
                    </td>
                  </tr>
                ) : (
                  items.map((item) => (
                    <tr key={item.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                      <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                        {item.coopId}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {item.description}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {item.quantity}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {formatCurrency(item.price)}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900 dark:text-dark-100">
                        {formatCurrency(item.price * item.quantity)}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {dayjs(item.buyingDate).format("MMM D, YYYY")}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4">
                        <span
                          className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
                            item.payment_status === 1
                              ? "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                              : "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200"
                          }`}
                        >
                          {item.payment_status === 1 ? "Paid" : "Unpaid"}
                        </span>
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                        <div className="flex items-center justify-end gap-2">
                          <Link to={`/business/items/${item.id}/edit`}>
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
                            onClick={() => handleDelete(item.id)}
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
        </Card>
      </div>
    </Page>
  );
}
