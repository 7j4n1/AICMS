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
      ],
    },
    // The app layout supports only the main layout. Avoid using it for other layouts.
    {
      Component: AppLayout,
      children: [
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
        },
      ],
    },
  ],
};

export { protectedRoutes };
