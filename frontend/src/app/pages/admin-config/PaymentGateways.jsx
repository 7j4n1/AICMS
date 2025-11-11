// Import Dependencies
import { useEffect, useState } from "react";
import { toast } from "sonner";

// Local Imports
import { Page } from "components/shared/Page";
import { Button, Card, Badge } from "components/ui";
import { paymentGatewaysAPI } from "services/api";

// ----------------------------------------------------------------------

export default function PaymentGateways() {
  const [gateways, setGateways] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchGateways();
  }, []);

  const fetchGateways = async () => {
    try {
      setLoading(true);
      const response = await paymentGatewaysAPI.getAll();
      setGateways(response.data?.data || []);
    } catch (err) {
      console.error("Error fetching gateways:", err);
      toast.error("Failed to load payment gateways");
    } finally {
      setLoading(false);
    }
  };

  const handleToggle = async (gateway) => {
    try {
      await paymentGatewaysAPI.update(gateway.id, {
        enabled: !gateway.enabled,
      });
      toast.success("Gateway updated successfully");
      fetchGateways();
    } catch {
      toast.error("Failed to update gateway");
    }
  };

  return (
    <Page title="Payment Gateways">
      <div className="max-w-4xl mx-auto space-y-5 my-6 px-4">
        <div>
          <h2 className="text-2xl font-bold text-gray-800 dark:text-dark-100">
            Payment Gateways
          </h2>
          <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
            Configure payment gateway integrations
          </p>
        </div>

        {loading ? (
          <div className="text-center py-8">Loading...</div>
        ) : (
          <div className="space-y-4">
            {gateways.map((gateway) => (
              <Card key={gateway.id} className="p-6">
                <div className="flex items-center justify-between">
                  <div className="flex-1">
                    <h3 className="text-lg font-semibold capitalize">
                      {gateway.name}
                    </h3>
                    <Badge color={gateway.enabled ? "success" : "primary"} className="mt-2">
                      {gateway.enabled ? "Enabled" : "Disabled"}
                    </Badge>
                  </div>
                  <Button
                    color={gateway.enabled ? "error" : "success"}
                    onClick={() => handleToggle(gateway)}
                  >
                    {gateway.enabled ? "Disable" : "Enable"}
                  </Button>
                </div>
              </Card>
            ))}
          </div>
        )}
      </div>
    </Page>
  );
}
