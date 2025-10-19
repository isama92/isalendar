import GuestLayout from '@/Components/GuestLayout';
import UserLayout from '@/Components/UserLayout';
import { usePage } from '@inertiajs/react';

const Layout = ({ children }) => {
    const { currentUser } = usePage().props;

    if (currentUser) {
        return <UserLayout>{children}</UserLayout>;
    }

    return <GuestLayout>{children}</GuestLayout>;
};

export default Layout;
