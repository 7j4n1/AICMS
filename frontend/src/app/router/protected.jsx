// Import Dependencies
import { Navigate } from "react-router";

// Local Imports
import { AppLayout } from "app/layouts/AppLayout";
import { DynamicLayout } from "app/layouts/DynamicLayout";
import AuthGuard from "middleware/AuthGuard";

// ----------------------------------------------------------------------

const protectedRoutes = {
  id: "protected",
  Component: AuthGuard,
  children: [
    // The dynamic layout supports both the main layout and the sideblock.
    {
      Component: DynamicLayout,
      children: [
        {
          index: true,
          element: <Navigate to="/dashboards" />,
        },
        {
          path: "dashboards",
          children: [
            {
              index: true,
              element: <Navigate to="/dashboards/home" />,
            },
            {
              path: "home",
              lazy: async () => ({
                Component: (await import("app/pages/dashboards/home")).default,
              }),
            },
          ],
        },
        // Account/Profile Routes
        {
          path: "dashboards/account",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/account")).default,
              }),
            },
          ],
        },
        // Members Routes
        {
          path: "members",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/members")).default,
              }),
            },
            {
              path: "create",
              lazy: async () => ({
                Component: (await import("app/pages/members/MemberForm")).default,
              }),
            },
            {
              path: ":id",
              lazy: async () => ({
                Component: (await import("app/pages/members/MemberDetail")).default,
              }),
            },
            {
              path: ":id/edit",
              lazy: async () => ({
                Component: (await import("app/pages/members/MemberForm")).default,
              }),
            },
          ],
        },
        // Loans Routes
        {
          path: "loans",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/loans")).default,
              }),
            },
            {
              path: "active",
              lazy: async () => ({
                Component: (await import("app/pages/loans/ActiveLoans")).default,
              }),
            },
            {
              path: "create",
              lazy: async () => ({
                Component: (await import("app/pages/loans/LoanForm")).default,
              }),
            },
            {
              path: ":id/edit",
              lazy: async () => ({
                Component: (await import("app/pages/loans/LoanForm")).default,
              }),
            },
          ],
        },
        // Payments Routes
        {
          path: "payments",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/payments")).default,
              }),
            },
            {
              path: "create",
              lazy: async () => ({
                Component: (await import("app/pages/payments/PaymentForm")).default,
              }),
            },
          ],
        },
        // Annual Fees Routes
        {
          path: "annual-fees",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/annual-fees")).default,
              }),
            },
            {
              path: "create",
              lazy: async () => ({
                Component: (await import("app/pages/annual-fees/AnnualFeeForm")).default,
              }),
            },
            {
              path: ":id/edit",
              lazy: async () => ({
                Component: (await import("app/pages/annual-fees/AnnualFeeForm")).default,
              }),
            },
          ],
        },
        // Business Routes
        {
          path: "business",
          children: [
            {
              path: "categories",
              children: [
                {
                  index: true,
                  lazy: async () => ({
                    Component: (await import("app/pages/business/categories")).default,
                  }),
                },
                {
                  path: "create",
                  lazy: async () => ({
                    Component: (await import("app/pages/business/categories/CategoryForm")).default,
                  }),
                },
                {
                  path: ":id/edit",
                  lazy: async () => ({
                    Component: (await import("app/pages/business/categories/CategoryForm")).default,
                  }),
                },
              ],
            },
            {
              path: "items",
              children: [
                {
                  index: true,
                  lazy: async () => ({
                    Component: (await import("app/pages/business/items")).default,
                  }),
                },
                {
                  path: "create",
                  lazy: async () => ({
                    Component: (await import("app/pages/business/items/ItemForm")).default,
                  }),
                },
                {
                  path: ":id/edit",
                  lazy: async () => ({
                    Component: (await import("app/pages/business/items/ItemForm")).default,
                  }),
                },
              ],
            },
          ],
        },
        // Admins Routes
        {
          path: "admins",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/admins")).default,
              }),
            },
            {
              path: "create",
              lazy: async () => ({
                Component: (await import("app/pages/admins/AdminForm")).default,
              }),
            },
            {
              path: ":id/edit",
              lazy: async () => ({
                Component: (await import("app/pages/admins/AdminForm")).default,
              }),
            },
          ],
        },
        // Support Tickets Routes
        {
          path: "support-tickets",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/support-tickets")).default,
              }),
            },
            {
              path: "create",
              lazy: async () => ({
                Component: (await import("app/pages/support-tickets/CreateTicket")).default,
              }),
            },
            {
              path: ":id",
              lazy: async () => ({
                Component: (await import("app/pages/support-tickets/TicketDetail")).default,
              }),
            },
          ],
        },
        // Payment Notifications Routes
        {
          path: "payment-notifications",
          children: [
            {
              index: true,
              lazy: async () => ({
                Component: (await import("app/pages/payment-notifications")).default,
              }),
            },
            {
              path: "create",
              lazy: async () => ({
                Component: (await import("app/pages/payment-notifications/CreateNotification")).default,
              }),
            },
            {
              path: ":id",
              lazy: async () => ({
                Component: (await import("app/pages/payment-notifications/NotificationDetail")).default,
              }),
            },
          ],
        },
        // Admin Configuration Routes
        {
          path: "admin-config",
          children: [
            {
              path: "system",
              lazy: async () => ({
                Component: (await import("app/pages/admin-config/SystemSettings")).default,
              }),
            },
            {
              path: "payment-gateways",
              lazy: async () => ({
                Component: (await import("app/pages/admin-config/PaymentGateways")).default,
              }),
            },
            {
              path: "savings-types",
              lazy: async () => ({
                Component: (await import("app/pages/admin-config/SavingsTypes")).default,
              }),
            },
            {
              path: "loan-eligibility",
              lazy: async () => ({
                Component: (await import("app/pages/admin-config/LoanEligibility")).default,
              }),
            },
          ],
        },
        // Settings Routes
        {
          path: "settings",
          lazy: async () => ({
            Component: (await import("app/pages/settings/Layout")).default,
          }),
          children: [
            {
              index: true,
              element: <Navigate to="/settings/general" />,
            },
            {
              path: "general",
              lazy: async () => ({
                Component: (await import("app/pages/settings/sections/General"))
                  .default,
              }),
            },
            {
              path: "appearance",
              lazy: async () => ({
                Component: (
                  await import("app/pages/settings/sections/Appearance")
                ).default,
              }),
            },
          ],
        }

      ],
    },
    // The app layout supports only the main layout. Avoid using it for other layouts.
    {
      Component: AppLayout,
      children: [
        
      ],
    },
  ],
};

export { protectedRoutes };
