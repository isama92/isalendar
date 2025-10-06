const StatCard = ({ label, value }) => {
    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h3 className="text-sm font-medium text-gray-500 dark:text-gray-400">{label}</h3>
            <p className="mt-2 text-3xl font-semibold text-gray-900 dark:text-gray-100">{value}</p>
        </div>
    );
};

export default StatCard;
