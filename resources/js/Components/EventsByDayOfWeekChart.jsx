import dayjs from 'dayjs';
import ReactECharts from 'echarts-for-react';

const EventsByDayOfWeekChart = ({ averagePerDayOfWeek }) => {
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
        <div className="overflow-hidden rounded-lg bg-white px-4 py-5 shadow-sm sm:p-6 dark:bg-gray-800/75 dark:inset-ring dark:inset-ring-white/10">
            <h3 className="text-base font-semibold text-gray-900 dark:text-white">
                {__('dashboard.events_by_day_of_week')}
            </h3>
            <div className="mt-5">
                <ReactECharts option={chartOptions} style={{ height: '300px' }} className="sm:h-[400px]" />
            </div>
        </div>
    );
};

export default EventsByDayOfWeekChart;
