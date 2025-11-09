// Import Dependencies
import { useEffect, useState } from "react";
import { toast } from "sonner";
import { PlusIcon, PencilIcon, TrashIcon } from "@heroicons/react/24/outline";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Badge } from "components/ui";
import { savingsTypesAPI } from "services/api";

// ----------------------------------------------------------------------

export default function SavingsTypes() {
  const [types, setTypes] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchTypes();
  }, []);

  const fetchTypes = async () => {
    try {
      setLoading(true);
      const response = await savingsTypesAPI.getAll();
      setTypes(response.data?.data || []);
    } catch (error) {
      console.error("Error fetching savings types:", error);
      toast.error("Failed to load savings types");
    } finally {
      setLoading(false);
    }
  };

  return (
    <Page title="Savings Types">
      <div className="max-w-4xl mx-auto space-y-5 my-6 px-4">
        <div className="flex items-center justify-between">
          <div>
            <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
              Savings Types
            </h2>
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
              Manage different savings categories
            </p>
          </div>
          <Button color="primary" className="gap-2">
            <PlusIcon className="size-5" />
            Add Type
          </Button>
        </div>

        {loading ? (
          <div className="text-center py-8">Loading...</div>
        ) : (
          <div className="space-y-4">
            {types.map((type) => (
              <Card key={type.id} className="p-6">
                <div className="flex items-center justify-between">
                  <div>
                    <h3 className="text-lg font-semibold">{type.name}</h3>
                    <p className="text-sm text-gray-500 dark:text-dark-300 mt-1">
                      {type.description}
                    </p>
                    <Badge color={type.active ? "success" : "default"} className="mt-2">
                      {type.active ? "Active" : "Inactive"}
                    </Badge>
                  </div>
                  <div className="flex gap-2">
                    <Button size="sm" variant="outlined">
                      <PencilIcon className="size-4" />
                    </Button>
                    <Button size="sm" variant="outlined" color="error">
                      <TrashIcon className="size-4" />
                    </Button>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        )}
      </div>
    </Page>
  );
}
