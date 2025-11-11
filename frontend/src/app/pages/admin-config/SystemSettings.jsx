// Import Dependencies
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { CloudArrowUpIcon } from "@heroicons/react/24/outline";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Input } from "components/ui";
import { configurationAPI } from "services/api";

// ----------------------------------------------------------------------

export default function SystemSettings() {
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [currentLogo, setCurrentLogo] = useState(null);

  const {
    register,
    handleSubmit,
    setValue,
  } = useForm();

  useEffect(() => {
    fetchConfigurations();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const fetchConfigurations = async () => {
    try {
      setLoading(true);
      const [configsResponse, logoResponse] = await Promise.all([
        configurationAPI.getAll(),
        configurationAPI.getLogo(),
      ]);

      const configs = configsResponse.data?.data || {};
      setCurrentLogo(logoResponse.data?.data?.url);

      // Set form values
      setValue("app_name", configs.app_name?.value || "");
      setValue("cooperative_name", configs.cooperative_name?.value || "");
      setValue("cooperative_initials", configs.cooperative_initials?.value || "");
      setValue("admin_charge", configs.admin_charge?.value || "");
      setValue("cooperative_account_name", configs.cooperative_account_name?.value || "");
      setValue("cooperative_account_number", configs.cooperative_account_number?.value || "");
      setValue("cooperative_bank_name", configs.cooperative_bank_name?.value || "");
    } catch (error) {
      console.error("Error fetching configurations:", error);
      toast.error("Failed to load settings");
    } finally {
      setLoading(false);
    }
  };

  const handleLogoChange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    try {
      const formData = new FormData();
      formData.append("logo", file);

      await configurationAPI.uploadLogo(formData);
      toast.success("Logo uploaded successfully");
      fetchConfigurations();
    } catch (error) {
      console.error("Error uploading logo:", error);
      toast.error("Failed to upload logo");
    }
  };

  const onSubmit = async (data) => {
    try {
      setSaving(true);

      const configurations = [
        { key: "app_name", value: data.app_name, type: "string" },
        { key: "cooperative_name", value: data.cooperative_name, type: "string" },
        { key: "cooperative_initials", value: data.cooperative_initials, type: "string" },
        { key: "admin_charge", value: data.admin_charge, type: "integer" },
        { key: "cooperative_account_name", value: data.cooperative_account_name, type: "string" },
        { key: "cooperative_account_number", value: data.cooperative_account_number, type: "string" },
        { key: "cooperative_bank_name", value: data.cooperative_bank_name, type: "string" },
      ];

      await configurationAPI.update(configurations);

      toast.success("Settings saved successfully");
    } catch (error) {
      console.error("Error saving settings:", error);
      toast.error("Failed to save settings");
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return (
      <Page title="System Settings">
        <div className="flex items-center justify-center h-64">
          <div className="text-gray-500">Loading...</div>
        </div>
      </Page>
    );
  }

  return (
    <Page title="System Settings">
      <div className="max-w-4xl mx-auto space-y-5 my-6 px-4">
        {/* Header */}
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            System Settings
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            Configure system-wide settings
          </p>
        </div>

        <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
          {/* Logo Upload */}
          <Card className="p-6">
            <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100 mb-4">
              Application Logo
            </h3>
            <div className="flex items-center gap-6">
              {currentLogo && (
                <div className="size-24 border rounded-lg overflow-hidden bg-gray-50 dark:bg-dark-600">
                  <img src={currentLogo} alt="Current Logo" className="size-full object-contain" />
                </div>
              )}
              <label className="cursor-pointer inline-flex items-center gap-2 px-4 py-2 border border-gray-300 dark:border-dark-500 rounded-lg hover:bg-gray-50 dark:hover:bg-dark-600">
                <CloudArrowUpIcon className="size-5" />
                <span className="text-sm">Upload Logo</span>
                <input
                  type="file"
                  accept="image/*"
                  onChange={handleLogoChange}
                  className="hidden"
                />
              </label>
            </div>
          </Card>

          {/* App Branding */}
          <Card className="p-6 space-y-4">
            <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100">
              Application Branding
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  App Name
                </label>
                <Input {...register("app_name")} placeholder="Application name" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Cooperative Name
                </label>
                <Input {...register("cooperative_name")} placeholder="Cooperative full name" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Cooperative Initials
                </label>
                <Input {...register("cooperative_initials")} placeholder="e.g., CS" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Admin Charge (₦)
                </label>
                <Input
                  type="number"
                  {...register("admin_charge")}
                  placeholder="Monthly admin charge"
                />
              </div>
            </div>
          </Card>

          {/* Bank Account Info */}
          <Card className="p-6 space-y-4">
            <h3 className="text-lg font-semibold text-gray-800 dark:text-dark-100">
              Cooperative Bank Account
            </h3>
            <div className="grid grid-cols-1 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Account Name
                </label>
                <Input {...register("cooperative_account_name")} placeholder="Account holder name" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Account Number
                </label>
                <Input {...register("cooperative_account_number")} placeholder="Account number" />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 dark:text-dark-200 mb-2">
                  Bank Name
                </label>
                <Input {...register("cooperative_bank_name")} placeholder="Bank name" />
              </div>
            </div>
          </Card>

          {/* Actions */}
          <div className="flex justify-end">
            <Button type="submit" color="primary" disabled={saving}>
              {saving ? "Saving..." : "Save Settings"}
            </Button>
          </div>
        </form>
      </div>
    </Page>
  );
}
