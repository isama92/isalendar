import { DISPLAY_DATE_FORMAT } from '@/utils/date';
import { CalendarIcon } from '@heroicons/react/24/outline';
import clsx from 'clsx';
import dayjs from 'dayjs';
import { useState } from 'react';
import { DayPicker } from 'react-day-picker';
import 'react-day-picker/style.css';

const DatePicker = ({
    value = null,
    onChange,
    disabled = false,
    error = null,
    id = null,
    name = null,
    className = '',
    placeholder = '',
    roundedClass = 'rounded-md',
    minDate = null,
}) => {
    const [isCalendarOpen, setIsCalendarOpen] = useState(false);

    // Handle date change
    const handleDateChange = newDate => {
        onChange(newDate);
        setIsCalendarOpen(false);
    };

    // Format display date
    const displayDate = value ? dayjs(value).format(DISPLAY_DATE_FORMAT) : '';

    // Configure disabled dates
    const disabledDates = minDate ? { before: minDate } : undefined;

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
                onClick={() => !disabled && setIsCalendarOpen(!isCalendarOpen)}
                onKeyDown={e => {
                    if (!disabled && (e.key === 'Enter' || e.key === ' ')) {
                        e.preventDefault();
                        setIsCalendarOpen(!isCalendarOpen);
                    }
                }}
            >
                <CalendarIcon className="h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" />
                <input
                    type="text"
                    id={id}
                    name={name}
                    value={displayDate}
                    placeholder={placeholder}
                    readOnly
                    disabled={disabled}
                    className="min-w-0 flex-1 border-none bg-transparent p-0 text-sm focus:ring-0 focus:outline-none"
                />
            </div>

            {/* Calendar Popup */}
            {isCalendarOpen && !disabled && (
                <>
                    <div
                        className="fixed inset-0 z-10"
                        onClick={() => setIsCalendarOpen(false)}
                        onKeyDown={e => {
                            if (e.key === 'Escape') {
                                setIsCalendarOpen(false);
                            }
                        }}
                        role="button"
                        tabIndex={-1}
                        aria-label="Close calendar"
                    />
                    <div className="absolute top-full z-20 mt-1 rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                        <DayPicker
                            mode="single"
                            selected={value}
                            onSelect={handleDateChange}
                            weekStartsOn={1}
                            disabled={disabledDates}
                        />
                    </div>
                </>
            )}
        </div>
    );
};

export default DatePicker;
