import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface DashboardProps {
    users: User[];
}

export default function Dashboard({ users }: DashboardProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
             <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border p-5">
                    <div className="overflow-x-auto overflow-y-auto max-h-[400px] p-2">
                    <h2>Users</h2>
                        <table className="min-w-full table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr className="bg-orange-900">
                            <th className="px-4 py-2 border border-orange-300">Name</th>
                            <th className="px-4 py-2 border border-orange-300">Email</th>
                            <th className="px-4 py-2 border border-orange-300">Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            {users.length > 0 ? (
                            // eslint-disable-next-line @typescript-eslint/no-explicit-any
                            users.map((user: any) => (
                                <tr key={user.id} className="border-b">
                                <td className="px-4 py-2 border border-orange-300">{user.name}</td>
                                <td className="px-4 py-2 border border-orange-300">{user.email}</td>
                                <td className="px-4 py-2 border border-orange-300">{user.role}</td>
                                </tr>
                            ))
                            ) : (
                            <tr>
                                <td colSpan={3} className="px-4 py-2 text-center border border-gray-300">
                                No users found.
                                </td>
                            </tr>
                            )}
                        </tbody>
                        </table>
                    </div>
                </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
