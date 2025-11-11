// Import Dependencies
import PropTypes from "prop-types";
import { Card } from "components/ui";

/**
 * Dashboard stat card widget
 */
export function StatCard({ title, value, icon: Icon, color = "primary", subtitle, trend }) {
  const colorClasses = {
    primary: "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400",
    success: "bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400",
    warning: "bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400",
    error: "bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400",
    info: "bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400",
  };

  return (
    <Card className="p-6">
      <div className="flex items-center justify-between">
        <div className="flex-1">
          <p className="text-sm font-medium text-gray-500 dark:text-dark-300">
            {title}
          </p>
          <p className="mt-2 text-3xl font-semibold text-gray-900 dark:text-dark-100">
            {value}
          </p>
          {subtitle && (
            <p className="mt-1 text-sm text-gray-500 dark:text-dark-400">
              {subtitle}
            </p>
          )}
          {trend && (
            <div className={`mt-2 text-sm ${trend.positive ? 'text-green-600' : 'text-red-600'}`}>
              {trend.value}
            </div>
          )}
        </div>
        {Icon && (
          <div className={`rounded-lg p-3 ${colorClasses[color]}`}>
            <Icon className="size-8" />
          </div>
        )}
      </div>
    </Card>
  );
}

StatCard.propTypes = {
  title: PropTypes.string.isRequired,
  value: PropTypes.oneOfType([PropTypes.string, PropTypes.number]).isRequired,
  icon: PropTypes.elementType,
  color: PropTypes.oneOf(["primary", "success", "warning", "error", "info"]),
  subtitle: PropTypes.string,
  trend: PropTypes.shape({
    value: PropTypes.string,
    positive: PropTypes.bool,
  }),
};
