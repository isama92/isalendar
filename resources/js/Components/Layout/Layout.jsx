import GuestLayout from '@/Components/Layout/GuestLayout';
import UserLayout from '@/Components/Layout/UserLayout';
import { usePage } from '@inertiajs/react';

const Layout = ({ children }) => {
    const { currentUser } = usePage().props;

    if (currentUser) {
        return <UserLayout>{children}</UserLayout>;
    }

    return <GuestLayout>{children}</GuestLayout>;
};

export default Layout;
