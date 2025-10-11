import clsx from 'clsx';

const StatCard = ({ label, value, className = '' }) => {
    return (
        <div
            className={clsx(
                'overflow-hidden rounded-lg bg-white px-4 py-5 shadow-sm sm:p-6 dark:bg-gray-800/75 dark:inset-ring dark:inset-ring-white/10',
                className
            )}
        >
            <dt className="truncate text-sm font-medium text-gray-500 dark:text-gray-400">{label}</dt>
            <dd className="mt-1 text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">{value}</dd>
        </div>
    );
};

export default StatCard;
