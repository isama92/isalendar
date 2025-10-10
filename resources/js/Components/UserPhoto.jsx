import { UserCircleIcon } from '@heroicons/react/24/solid';
import { usePage } from '@inertiajs/react';
import clsx from 'clsx';
import { useEffect, useState } from 'react';

const UserPhoto = ({ className, iconClassName }) => {
    const { user } = usePage().props;
    const [hasPhoto, setHasPhoto] = useState(false);

    useEffect(() => {
        fetch(route('profile.photo'))
            .then(response => {
                if (response.ok) {
                    setHasPhoto(true);
                }
            })
            .catch(() => {
                // No photo exists
            });
    }, []);

    if (hasPhoto) {
        return (
            <img
                src={route('profile.photo')}
                alt={user.full_name}
                className={clsx('rounded-full object-cover', className)}
            />
        );
    }

    return <UserCircleIcon aria-hidden="true" className={clsx('text-gray-300 dark:text-gray-500', iconClassName)} />;
};

export default UserPhoto;
