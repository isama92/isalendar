const Dashboard = () => {
    return (
        <div className="rounded-lg bg-white p-6 shadow">
            <h2 className="text-2xl font-semibold text-gray-900">{__('dashboard.dashboard')}</h2>
            <p className="mt-2 text-gray-600">{__('dashboard.welcome_message')}</p>
        </div>
    );
};

export default Dashboard;
