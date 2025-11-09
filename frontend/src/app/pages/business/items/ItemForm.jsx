// Import Dependencies
import { useEffect, useState, useCallback } from "react";
import { useNavigate, useParams } from "react-router";
import { useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import * as yup from "yup";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { itemsAPI, categoriesAPI } from "services/api";

// ----------------------------------------------------------------------

const schema = yup.object().shape({
  coopId: yup.string().required("Coop ID is required"),
  category_id: yup.number().required("Category is required"),
  quantity: yup.number().required("Quantity is required").min(1),
  price: yup.number().required("Price is required").min(0),
  description: yup.string().required("Description is required"),
  buyingDate: yup.string().required("Buying date is required"),
  payment_timeframe: yup.number().required("Payment timeframe is required").min(1),
});

export default function ItemForm() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [categories, setCategories] = useState([]);
  const isEdit = Boolean(id);

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm({
    resolver: yupResolver(schema),
  });

  const fetchCategories = useCallback(async () => {
    try {
      const response = await categoriesAPI.getAll({ per_page: 100 });
      const data = response.data?.data || [];
      setCategories(data);
    } catch (error) {
      console.error("Error fetching categories:", error);
    }
  }, []);

  const fetchItem = useCallback(async () => {
    if (!id) return;
    try {
      const response = await itemsAPI.getById(id);
      const item = response.data?.data || response.data;
      reset(item);
    } catch (error) {
      console.error("Error fetching item:", error);
      toast.error("Failed to load item details");
    }
  }, [id, reset]);

  useEffect(() => {
    fetchCategories();
    if (isEdit) {
      fetchItem();
    }
  }, [isEdit, fetchItem, fetchCategories]);

  const onSubmit = async (data) => {
    try {
      setLoading(true);
      if (isEdit) {
        await itemsAPI.update(id, data);
        toast.success("Item updated successfully");
      } else {
        await itemsAPI.create(data);
        toast.success("Item created successfully");
      }
      navigate("/business/items");
    } catch (error) {
      console.error("Error saving item:", error);
      const errorMsg = error.response?.data?.message || error.message || "Failed to save item";
      toast.error(errorMsg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Page title={isEdit ? "Edit Item" : "Create Item"}>
      <div className="space-y-5">
        {/* Header */}
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            {isEdit ? "Edit Item" : "Create New Item"}
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            {isEdit ? "Update item information" : "Add a new business item"}
          </p>
        </div>

        {/* Form */}
        <Card className="p-6">
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            {/* Item Information */}
            <div>
              <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
                Item Information
              </h3>
              <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                <Input
                  label="Coop ID"
                  placeholder="Enter member Coop ID"
                  {...register("coopId")}
                  error={errors?.coopId?.message}
                  disabled={isEdit}
                />
                <div>
                  <label className="mb-2 block text-sm font-medium text-gray-700 dark:text-dark-100">
                    Category
                  </label>
                  <select
                    {...register("category_id")}
                    className="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-dark-500 dark:bg-dark-700"
                  >
                    <option value="">Select a category</option>
                    {categories.map((category) => (
                      <option key={category.id} value={category.id}>
                        {category.name}
                      </option>
                    ))}
                  </select>
                  {errors?.category_id && (
                    <p className="mt-1 text-xs text-red-600">{errors.category_id.message}</p>
                  )}
                </div>
                <Input
                  label="Quantity"
                  type="number"
                  placeholder="Enter quantity"
                  {...register("quantity")}
                  error={errors?.quantity?.message}
                />
                <Input
                  label="Price"
                  type="number"
                  placeholder="Enter price per unit"
                  {...register("price")}
                  error={errors?.price?.message}
                />
                <Input
                  label="Buying Date"
                  type="date"
                  {...register("buyingDate")}
                  error={errors?.buyingDate?.message}
                />
                <Input
                  label="Payment Timeframe (days)"
                  type="number"
                  placeholder="Enter payment timeframe in days"
                  {...register("payment_timeframe")}
                  error={errors?.payment_timeframe?.message}
                />
                <div className="md:col-span-2">
                  <label className="mb-2 block text-sm font-medium text-gray-700 dark:text-dark-100">
                    Description
                  </label>
                  <textarea
                    {...register("description")}
                    rows="3"
                    className="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-dark-500 dark:bg-dark-700"
                    placeholder="Enter item description"
                  />
                  {errors?.description && (
                    <p className="mt-1 text-xs text-red-600">{errors.description.message}</p>
                  )}
                </div>
              </div>
            </div>

            {/* Actions */}
            <div className="flex gap-3">
              <Button
                type="submit"
                color="primary"
                disabled={loading}
              >
                {loading ? "Saving..." : isEdit ? "Update Item" : "Create Item"}
              </Button>
              <Button
                type="button"
                variant="outlined"
                onClick={() => navigate("/business/items")}
              >
                Cancel
              </Button>
            </div>
          </form>
        </Card>
      </div>
    </Page>
  );
}
