// Import Dependencies
import { useEffect, useState } from "react";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Badge } from "components/ui";
import { loanEligibilityAPI } from "services/api";

// ----------------------------------------------------------------------

export default function LoanEligibility() {
  const [settings, setSettings] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchSettings();
  }, []);

  const fetchSettings = async () => {
    try {
      setLoading(true);
      const response = await loanEligibilityAPI.getAll();
      setSettings(response.data?.data || []);
    } catch (error) {
      console.error("Error fetching settings:", error);
      toast.error("Failed to load loan eligibility settings");
    } finally {
      setLoading(false);
    }
  };

  const handleActivate = async (setting) => {
    try {
      await loanEligibilityAPI.update(setting.id, { active: true });
      toast.success("Formula activated successfully");
      fetchSettings();
    } catch  {
      toast.error("Failed to activate formula");
    }
  };

  return (
    <Page title="Loan Eligibility">
      <div className="max-w-4xl mx-auto space-y-5 my-6 px-4">
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            Loan Eligibility Settings
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            Configure loan eligibility calculation formulas
          </p>
        </div>

        {loading ? (
          <div className="text-center py-8">Loading...</div>
        ) : (
          <div className="space-y-4">
            {settings.map((setting) => (
              <Card key={setting.id} className="p-6">
                <div className="flex items-center justify-between">
                  <div className="flex-1">
                    <div className="flex items-center gap-3">
                      <h3 className="text-lg font-semibold">{setting.name}</h3>
                      {setting.active && (
                        <Badge color="success">Active</Badge>
                      )}
                    </div>
                    <p className="text-sm text-gray-500 dark:text-dark-300 mt-1">
                      {setting.description}
                    </p>
                    <code className="mt-2 inline-block px-3 py-1 bg-gray-100 dark:bg-dark-600 rounded text-sm">
                      {setting.formula}
                    </code>
                  </div>
                  {!setting.active && (
                    <Button
                      color="primary"
                      onClick={() => handleActivate(setting)}
                    >
                      Activate
                    </Button>
                  )}
                </div>
              </Card>
            ))}
          </div>
        )}
      </div>
    </Page>
  );
}
