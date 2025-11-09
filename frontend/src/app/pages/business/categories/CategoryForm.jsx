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
import { categoriesAPI } from "services/api";

// ----------------------------------------------------------------------

const schema = yup.object().shape({
  name: yup.string().required("Category name is required"),
});

export default function CategoryForm() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const isEdit = Boolean(id);

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm({
    resolver: yupResolver(schema),
  });

  const fetchCategory = useCallback(async () => {
    if (!id) return;
    try {
      const response = await categoriesAPI.getById(id);
      const category = response.data?.data || response.data;
      reset(category);
    } catch (error) {
      console.error("Error fetching category:", error);
      toast.error("Failed to load category details");
    }
  }, [id, reset]);

  useEffect(() => {
    if (isEdit) {
      fetchCategory();
    }
  }, [isEdit, fetchCategory]);

  const onSubmit = async (data) => {
    try {
      setLoading(true);
      if (isEdit) {
        await categoriesAPI.update(id, data);
        toast.success("Category updated successfully");
      } else {
        await categoriesAPI.create(data);
        toast.success("Category created successfully");
      }
      navigate("/business/categories");
    } catch (error) {
      console.error("Error saving category:", error);
      const errorMsg = error.response?.data?.message || error.message || "Failed to save category";
      toast.error(errorMsg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Page title={isEdit ? "Edit Category" : "Create Category"}>
      <div className="space-y-5 mx-4 my-4">
        {/* Header */}
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            {isEdit ? "Edit Category" : "Create New Category"}
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            {isEdit ? "Update category information" : "Add a new item category"}
          </p>
        </div>

        {/* Form */}
        <Card className="p-6">
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            <Input
              label="Category Name"
              placeholder="Enter category name"
              {...register("name")}
              error={errors?.name?.message}
            />

            {/* Actions */}
            <div className="flex gap-3">
              <Button
                type="submit"
                color="primary"
                disabled={loading}
              >
                {loading ? "Saving..." : isEdit ? "Update Category" : "Create Category"}
              </Button>
              <Button
                type="button"
                variant="outlined"
                onClick={() => navigate("/business/categories")}
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
