// Import Dependencies
import { Link } from "react-router";
import PropTypes from "prop-types";
import { ArrowRightIcon } from "@heroicons/react/24/outline";
import { Card } from "components/ui";

/**
 * Quick action card widget
 */
export function QuickActionCard({ title, description, icon: Icon, to, color = "primary" }) {
  const colorClasses = {
    primary: "bg-blue-500 text-white hover:bg-blue-600",
    success: "bg-green-500 text-white hover:bg-green-600",
    warning: "bg-yellow-500 text-white hover:bg-yellow-600",
    error: "bg-red-500 text-white hover:bg-red-600",
    info: "bg-indigo-500 text-white hover:bg-indigo-600",
  };

  const iconColorClasses = {
    primary: "bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400",
    success: "bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400",
    warning: "bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400",
    error: "bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400",
    info: "bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400",
  };

  return (
    <Link to={to}>
      <Card className="p-6 hover:shadow-lg transition-shadow cursor-pointer">
        <div className="flex items-start gap-4">
          {Icon && (
            <div className={`rounded-lg p-3 ${iconColorClasses[color]}`}>
              <Icon className="size-6" />
            </div>
          )}
          <div className="flex-1">
            <h3 className="text-lg font-semibold text-gray-900 dark:text-dark-100">
              {title}
            </h3>
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-300">
              {description}
            </p>
          </div>
          <ArrowRightIcon className="size-5 text-gray-400" />
        </div>
      </Card>
    </Link>
  );
}

QuickActionCard.propTypes = {
  title: PropTypes.string.isRequired,
  description: PropTypes.string.isRequired,
  icon: PropTypes.elementType,
  to: PropTypes.string.isRequired,
  color: PropTypes.oneOf(["primary", "success", "warning", "error", "info"]),
};
