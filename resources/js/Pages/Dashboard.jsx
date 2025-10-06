const Dashboard = () => {
    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h2 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{__('dashboard.dashboard')}</h2>
            <p className="mt-2 text-gray-600 dark:text-gray-300">{__('dashboard.welcome_message')}</p>
        </div>
    );
};

export default Dashboard;
