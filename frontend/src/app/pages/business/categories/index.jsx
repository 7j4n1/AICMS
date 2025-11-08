// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { Link } from "react-router";
import { PlusIcon, MagnifyingGlassIcon, PencilIcon, TrashIcon } from "@heroicons/react/24/outline";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { categoriesAPI } from "services/api";

// ----------------------------------------------------------------------

export default function CategoriesList() {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  const fetchCategories = useCallback(async () => {
    try {
      setLoading(true);
      const response = await categoriesAPI.getAll({
        search,
        per_page: 25,
      });
      
      const data = response.data?.data || [];
      setCategories(data);
    } catch (error) {
      console.error("Error fetching categories:", error);
      toast.error("Failed to load categories");
    } finally {
      setLoading(false);
    }
  }, [search]);

  useEffect(() => {
    fetchCategories();
  }, [fetchCategories]);

  const handleDelete = async (id) => {
    if (!window.confirm("Are you sure you want to delete this category?")) {
      return;
    }

    try {
      await categoriesAPI.delete(id);
      toast.success("Category deleted successfully");
      fetchCategories();
    } catch (error) {
      console.error("Error deleting category:", error);
      const errorMsg = error.response?.data?.message || "Failed to delete category";
      toast.error(errorMsg);
    }
  };

  return (
    <Page title="Categories">
      <div className="space-y-5">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
              Categories Management
            </h2>
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
              Manage item categories
            </p>
          </div>
          <Link to="/business/categories/create">
            <Button color="primary" className="gap-2">
              <PlusIcon className="size-5" />
              Add Category
            </Button>
          </Link>
        </div>

        {/* Search */}
        <Card className="p-5">
          <Input
            placeholder="Search by category name..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            prefix={
              <MagnifyingGlassIcon className="size-5 text-gray-400" />
            }
          />
        </Card>

        {/* Categories Table */}
        <Card className="overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50 dark:bg-dark-600">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    ID
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Name
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-dark-300">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200 bg-white dark:divide-dark-500 dark:bg-dark-700">
                {loading ? (
                  <tr>
                    <td colSpan="3" className="px-6 py-4 text-center">
                      Loading...
                    </td>
                  </tr>
                ) : categories.length === 0 ? (
                  <tr>
                    <td colSpan="3" className="px-6 py-4 text-center text-gray-500">
                      No categories found
                    </td>
                  </tr>
                ) : (
                  categories.map((category) => (
                    <tr key={category.id} className="hover:bg-gray-50 dark:hover:bg-dark-600">
                      <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-dark-100">
                        {category.id}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-dark-300">
                        {category.name}
                      </td>
                      <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                        <div className="flex items-center justify-end gap-2">
                          <Link to={`/business/categories/${category.id}/edit`}>
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
                            onClick={() => handleDelete(category.id)}
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
