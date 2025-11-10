// Import Dependencies
import { Outlet } from "react-router";

// Local Imports
import { Page } from "components/shared/Page";
// import { Header } from "app/layouts/MainLayout/Header";
// import { Sidebar } from "./Sidebar";
import { Card } from "components/ui";

// ----------------------------------------------------------------------

export default function Settings() {
  return (
    <Page title="Setting">
      <div className="transition-content w-full px-[--margin-x] pt-5 lg:pt-6 space-y-6 mb-5">
        <Card className="h-full w-full p-4 sm:px-5 2xl:mx-auto 2xl:max-w-5xl">
          <Outlet />
        </Card>
      </div>
    </Page>
  );
}
