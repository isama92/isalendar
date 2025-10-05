import { Link, router, usePage } from '@inertiajs/react';

const Layout = ({ children }) => {
    const { user } = usePage().props;

    const handleLogout = () => {
        router.post(route('logout'));
    };

    return (
        <div className="flex min-h-screen bg-gray-50">
            {/* Sidebar */}
            {user && (
                <aside className="w-64 border-r border-gray-200 bg-white">
                    <div className="flex h-16 items-center justify-center border-b border-gray-200 px-6">
                        <img src="/assets/logo.png" alt="Isalendar" className="h-8" />
                    </div>
                    <nav className="p-4">
                        <Link
                            href={route('dashboard')}
                            className="mb-2 block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >
                            Dashboard
                        </Link>
                        <Link
                            href={route('events.index')}
                            className="block rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
                        >
                            Events
                        </Link>
                    </nav>
                </aside>
            )}

            {/* Main Content */}
            <div className="flex flex-1 flex-col">
                {/* Topbar */}
                <header className="border-b border-gray-200 bg-white">
                    {user && (
                        <div className="flex h-16 items-center justify-end px-6">
                            <div className="flex items-center gap-4">
                                <span className="text-sm text-gray-700">{user.name}</span>
                                <button
                                    onClick={handleLogout}
                                    className="rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                                >
                                    Logout
                                </button>
                            </div>
                        </div>
                    )}
                </header>

                {/* Page Content */}
                <main className="flex-1 p-6">{children}</main>
            </div>
        </div>
    );
};

export default Layout;
