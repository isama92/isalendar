import { ClockIcon } from '@heroicons/react/24/outline';
import clsx from 'clsx';
import { useEffect, useRef, useState } from 'react';

const TimePicker = ({
    value = '',
    onChange,
    error = null,
    id = null,
    name = null,
    disabled = false,
    className = '',
    minuteInterval = 1,
    roundedClass = 'rounded-md',
}) => {
    const [isOpen, setIsOpen] = useState(false);
    const [tempHour, setTempHour] = useState('12');
    const [tempMinute, setTempMinute] = useState('00');
    const [tempPeriod, setTempPeriod] = useState('AM');

    const hourRef = useRef(null);
    const minuteRef = useRef(null);
    const periodRef = useRef(null);

    // Format time for display (24-hour format)
    const formatTime = time24 => {
        if (!time24) return '';
        return time24; // Simply return as-is in 24-hour format
    };

    // Convert hour/minute/period to 24-hour format
    const formatTo24Hour = (hour, minute, period) => {
        let hour24 = parseInt(hour, 10);
        if (period === 'AM' && hour24 === 12) {
            hour24 = 0;
        } else if (period === 'PM' && hour24 !== 12) {
            hour24 += 12;
        }
        return `${hour24.toString().padStart(2, '0')}:${minute}`;
    };

    // Initialize temp values when dropdown opens
    useEffect(() => {
        if (isOpen) {
            if (value) {
                const [hours, minutes] = value.split(':');
                const hour = parseInt(hours, 10);
                const minute = parseInt(minutes, 10);
                const period = hour >= 12 ? 'PM' : 'AM';
                const hour12 = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;

                // Snap to nearest interval
                const snappedMinute = Math.round(minute / minuteInterval) * minuteInterval;
                const finalMinute = snappedMinute >= 60 ? 0 : snappedMinute;

                setTempHour(hour12.toString());
                setTempMinute(finalMinute.toString().padStart(2, '0'));
                setTempPeriod(period);
            } else {
                setTempHour('12');
                setTempMinute('00');
                setTempPeriod('AM');
            }
        }
    }, [isOpen, value, minuteInterval]);

    // Scroll to selected values when dropdown opens
    useEffect(() => {
        if (isOpen) {
            setTimeout(() => {
                scrollToSelected(hourRef, tempHour);
                scrollToSelected(minuteRef, tempMinute);
                scrollToSelected(periodRef, tempPeriod);
            }, 0);
        }
    }, [isOpen, tempHour, tempMinute, tempPeriod]);

    const scrollToSelected = (ref, value) => {
        if (!ref.current) return;
        const selectedElement = ref.current.querySelector(`[data-value="${value}"]`);
        if (selectedElement) {
            selectedElement.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }
    };

    const handleOk = () => {
        const time24 = formatTo24Hour(tempHour, tempMinute, tempPeriod);
        onChange(time24);
        setIsOpen(false);
    };

    const handleCancel = () => {
        setIsOpen(false);
    };

    // Generate options
    const hours = Array.from({ length: 12 }, (_, i) => (i + 1).toString());
    const minutes = Array.from({ length: Math.floor(60 / minuteInterval) }, (_, i) =>
        (i * minuteInterval).toString().padStart(2, '0')
    );
    const periods = ['AM', 'PM'];

    const displayValue = formatTime(value);

    return (
        <div className={clsx('relative', className)}>
            <div
                role="button"
                tabIndex={disabled ? -1 : 0}
                className={clsx(
                    'flex items-center gap-2 border px-3 py-2 text-sm',
                    roundedClass,
                    error
                        ? 'border-red-300 bg-white text-red-900 dark:border-red-500/50 dark:bg-white/5 dark:text-red-400'
                        : 'border-gray-300 bg-white text-gray-900 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100',
                    disabled ? 'cursor-not-allowed opacity-50' : 'cursor-pointer'
                )}
                onClick={() => !disabled && setIsOpen(!isOpen)}
                onKeyDown={e => {
                    if (!disabled && (e.key === 'Enter' || e.key === ' ')) {
                        e.preventDefault();
                        setIsOpen(!isOpen);
                    }
                }}
            >
                <ClockIcon className="h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" />
                <input
                    type="text"
                    id={id}
                    name={name}
                    value={displayValue}
                    placeholder={__('events.select_time')}
                    readOnly
                    disabled={disabled}
                    className="min-w-0 flex-1 border-none bg-transparent p-0 text-sm focus:ring-0 focus:outline-none"
                />
            </div>

            {/* Dropdown */}
            {isOpen && !disabled && (
                <>
                    <div
                        className="fixed inset-0 z-10"
                        onClick={handleCancel}
                        onKeyDown={e => {
                            if (e.key === 'Escape') {
                                handleCancel();
                            }
                        }}
                        role="button"
                        tabIndex={-1}
                        aria-label="Close time picker"
                    />
                    <div className="absolute top-full z-20 mt-1 w-64 rounded-lg border border-gray-200 bg-white shadow-lg ltr:right-0 rtl:left-0 dark:border-gray-700 dark:bg-gray-800">
                        {/* Scrollable columns */}
                        <div className="flex gap-2 border-b border-gray-200 p-4 dark:border-gray-700">
                            {/* Hours */}
                            <div className="flex-1">
                                <div className="text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {__('events.hour')}
                                </div>
                                <div
                                    ref={hourRef}
                                    className="scrollbar-thin scrollbar-track-gray-100 scrollbar-thumb-gray-300 dark:scrollbar-track-gray-700 dark:scrollbar-thumb-gray-600 mt-2 max-h-48 overflow-y-auto"
                                >
                                    {hours.map(hour => (
                                        <button
                                            key={hour}
                                            type="button"
                                            data-value={hour}
                                            onClick={() => setTempHour(hour)}
                                            className={clsx(
                                                'w-full cursor-pointer rounded px-3 py-2 text-sm transition-colors',
                                                tempHour === hour
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'text-gray-900 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-700'
                                            )}
                                        >
                                            {hour}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Minutes */}
                            <div className="flex-1">
                                <div className="text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    {__('events.minute')}
                                </div>
                                <div
                                    ref={minuteRef}
                                    className="scrollbar-thin scrollbar-track-gray-100 scrollbar-thumb-gray-300 dark:scrollbar-track-gray-700 dark:scrollbar-thumb-gray-600 mt-2 max-h-48 overflow-y-auto"
                                >
                                    {minutes.map(minute => (
                                        <button
                                            key={minute}
                                            type="button"
                                            data-value={minute}
                                            onClick={() => setTempMinute(minute)}
                                            className={clsx(
                                                'w-full cursor-pointer rounded px-3 py-2 text-sm transition-colors',
                                                tempMinute === minute
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'text-gray-900 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-700'
                                            )}
                                        >
                                            {minute}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* AM/PM */}
                            <div className="flex-1">
                                <div className="text-center text-xs font-medium text-gray-500 dark:text-gray-400">
                                    &nbsp;
                                </div>
                                <div
                                    ref={periodRef}
                                    className="scrollbar-thin scrollbar-track-gray-100 scrollbar-thumb-gray-300 dark:scrollbar-track-gray-700 dark:scrollbar-thumb-gray-600 mt-2 max-h-48 overflow-y-auto"
                                >
                                    {periods.map(period => (
                                        <button
                                            key={period}
                                            type="button"
                                            data-value={period}
                                            onClick={() => setTempPeriod(period)}
                                            className={clsx(
                                                'w-full cursor-pointer rounded px-3 py-2 text-sm transition-colors',
                                                tempPeriod === period
                                                    ? 'bg-indigo-600 text-white'
                                                    : 'text-gray-900 hover:bg-gray-100 dark:text-gray-100 dark:hover:bg-gray-700'
                                            )}
                                        >
                                            {period}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Action buttons */}
                        <div className="flex gap-2 p-4">
                            <button
                                type="button"
                                onClick={handleCancel}
                                className="flex-1 cursor-pointer rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                            >
                                {__('actions.cancel')}
                            </button>
                            <button
                                type="button"
                                onClick={handleOk}
                                className="flex-1 cursor-pointer rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600"
                            >
                                {__('actions.confirm')}
                            </button>
                        </div>
                    </div>
                </>
            )}
        </div>
    );
};

export default TimePicker;
