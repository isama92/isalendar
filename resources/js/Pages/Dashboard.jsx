import { usePage } from '@inertiajs/react';
import dayjs from 'dayjs';
import ReactECharts from 'echarts-for-react';
import StatCard from '../Components/StatCard';

const Dashboard = () => {
    const { totalEvents, eventsToday, eventsTomorrow, eventsThisWeek, eventsNextWeek, averagePerDayOfWeek } =
        usePage().props;

    const dayLabels = Array.from({ length: 7 }, (_, i) =>
        dayjs()
            .day(i + 1)
            .format('dddd')
    );

    const chartOptions = {
        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'shadow',
            },
        },
        xAxis: {
            type: 'category',
            data: dayLabels,
            axisLabel: {
                color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#4B5563',
            },
        },
        yAxis: {
            type: 'value',
            axisLabel: {
                color: document.documentElement.classList.contains('dark') ? '#D1D5DB' : '#4B5563',
            },
        },
        series: [
            {
                name: __('dashboard.events'),
                type: 'bar',
                data: averagePerDayOfWeek,
                itemStyle: {
                    color: '#3B82F6',
                },
            },
        ],
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true,
        },
    };

    return (
        <div className="space-y-6">
            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h2 className="text-2xl font-semibold text-gray-900 dark:text-gray-100">{__('dashboard.dashboard')}</h2>
                <p className="mt-2 text-gray-600 dark:text-gray-300">{__('dashboard.welcome_message')}</p>
            </div>

            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <StatCard label={__('dashboard.total_events')} value={totalEvents} />

                <StatCard label={__('dashboard.events_today')} value={eventsToday} />

                <StatCard label={__('dashboard.events_tomorrow')} value={eventsTomorrow} />

                <StatCard label={__('dashboard.events_this_week')} value={eventsThisWeek} />

                <StatCard label={__('dashboard.events_next_week')} value={eventsNextWeek} />
            </div>

            <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
                <h3 className="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">
                    {__('dashboard.events_by_day_of_week')}
                </h3>
                <ReactECharts option={chartOptions} style={{ height: '400px' }} />
            </div>
        </div>
    );
};

export default Dashboard;
