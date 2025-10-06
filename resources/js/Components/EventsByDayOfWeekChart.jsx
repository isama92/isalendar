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
        <div className="rounded-lg bg-white p-4 shadow sm:p-6 dark:bg-gray-800">
            <h3 className="mb-4 text-base font-semibold text-gray-900 sm:text-lg dark:text-gray-100">
                {__('dashboard.events_by_day_of_week')}
            </h3>
            <ReactECharts option={chartOptions} style={{ height: '300px' }} className="sm:h-[400px]" />
        </div>
    );
};

export default EventsByDayOfWeekChart;
