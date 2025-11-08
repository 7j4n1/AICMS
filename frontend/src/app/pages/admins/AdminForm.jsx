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
import { adminsAPI } from "services/api";

// ----------------------------------------------------------------------

const schema = yup.object().shape({
  name: yup.string().required("Name is required"),
  username: yup.string().required("Username is required"),
  email: yup.string().email("Invalid email").required("Email is required"),
  password: yup.string().when("$isEdit", {
    is: false,
    then: (schema) => schema.required("Password is required").min(6, "Password must be at least 6 characters"),
    otherwise: (schema) => schema.nullable(),
  }),
  coopId: yup.string().nullable(),
  role: yup.string().required("Role is required"),
});

export default function AdminForm() {
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
    context: { isEdit },
  });

  const fetchAdmin = useCallback(async () => {
    if (!id) return;
    try {
      const response = await adminsAPI.getById(id);
      const admin = response.data?.data || response.data;
      reset(admin);
    } catch (error) {
      console.error("Error fetching admin:", error);
      toast.error("Failed to load admin details");
    }
  }, [id, reset]);

  useEffect(() => {
    if (isEdit) {
      fetchAdmin();
    }
  }, [isEdit, fetchAdmin]);

  const onSubmit = async (data) => {
    try {
      setLoading(true);
      // Remove password if it's empty in edit mode
      if (isEdit && !data.password) {
        delete data.password;
      }
      
      if (isEdit) {
        await adminsAPI.update(id, data);
        toast.success("Admin updated successfully");
      } else {
        await adminsAPI.create(data);
        toast.success("Admin created successfully");
      }
      navigate("/admins");
    } catch (error) {
      console.error("Error saving admin:", error);
      const errorMsg = error.response?.data?.message || error.message || "Failed to save admin";
      toast.error(errorMsg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Page title={isEdit ? "Edit Admin" : "Create Admin"}>
      <div className="space-y-5">
        {/* Header */}
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            {isEdit ? "Edit Admin" : "Create New Admin"}
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            {isEdit ? "Update admin information" : "Add a new system administrator"}
          </p>
        </div>

        {/* Form */}
        <Card className="p-6">
          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            {/* Admin Information */}
            <div>
              <h3 className="mb-4 text-lg font-semibold text-gray-700 dark:text-dark-100">
                Admin Information
              </h3>
              <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                <Input
                  label="Full Name"
                  placeholder="Enter full name"
                  {...register("name")}
                  error={errors?.name?.message}
                />
                <Input
                  label="Username"
                  placeholder="Enter username"
                  {...register("username")}
                  error={errors?.username?.message}
                />
                <Input
                  label="Email"
                  type="email"
                  placeholder="Enter email address"
                  {...register("email")}
                  error={errors?.email?.message}
                />
                <Input
                  label={isEdit ? "Password (leave blank to keep current)" : "Password"}
                  type="password"
                  placeholder="Enter password"
                  {...register("password")}
                  error={errors?.password?.message}
                />
                <Input
                  label="Coop ID (Optional)"
                  placeholder="Enter Coop ID"
                  {...register("coopId")}
                  error={errors?.coopId?.message}
                />
                <div>
                  <label className="mb-2 block text-sm font-medium text-gray-700 dark:text-dark-100">
                    Role
                  </label>
                  <select
                    {...register("role")}
                    className="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-dark-500 dark:bg-dark-700"
                  >
                    <option value="">Select a role</option>
                    <option value="superadmin">Super Admin</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                  </select>
                  {errors?.role && (
                    <p className="mt-1 text-xs text-red-600">{errors.role.message}</p>
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
                {loading ? "Saving..." : isEdit ? "Update Admin" : "Create Admin"}
              </Button>
              <Button
                type="button"
                variant="outlined"
                onClick={() => navigate("/admins")}
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
