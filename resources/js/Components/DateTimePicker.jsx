import DatePicker from '@/Components/DatePicker';
import TimePicker from '@/Components/TimePicker';
import dayjs from 'dayjs';

const DateTimePicker = ({
    value = '',
    onChange,
    allDay = false,
    error = null,
    id = null,
    name = null,
    disabled = false,
    className = '',
    minuteInterval = 1,
    minDate = null,
}) => {
    // Parse the incoming value (YYYY-MM-DD HH:mm:ss or YYYY-MM-DD)
    const parseValue = () => {
        if (!value) return { date: null, time: '' };

        const dayjsDate = dayjs(value);
        if (!dayjsDate.isValid()) return { date: null, time: '' };

        return {
            date: dayjsDate.toDate(),
            time: allDay ? '' : dayjsDate.format('HH:mm'),
        };
    };

    const { date, time } = parseValue();

    // Handle date change
    const handleDateChange = newDate => {
        if (!newDate) {
            onChange('');
            return;
        }

        const dateStr = dayjs(newDate).format('YYYY-MM-DD');

        if (allDay) {
            onChange(dateStr);
        } else {
            const timeStr = time || '00:00';
            onChange(`${dateStr} ${timeStr}:00`);
        }
    };

    // Handle time change
    const handleTimeChange = newTime => {
        // If no date is selected, use today's date
        const currentDate = date || new Date();
        const dateStr = dayjs(currentDate).format('YYYY-MM-DD');
        const timeStr = newTime || '00:00';
        onChange(`${dateStr} ${timeStr}:00`);
    };

    return (
        <div className={className}>
            <div className="grid grid-cols-2">
                {/* Date Input with Calendar */}
                <div className={allDay ? 'col-span-2' : '-mr-px'}>
                    <DatePicker
                        id={id}
                        name={name}
                        value={date}
                        onChange={handleDateChange}
                        disabled={disabled}
                        error={error}
                        placeholder={__('events.select_date')}
                        roundedClass={allDay ? 'rounded-md' : 'rounded-l-md'}
                        minDate={minDate}
                    />
                </div>

                {/* Time Picker - only show if not all day */}
                {!allDay && (
                    <div className="-mt-px sm:-mt-0">
                        <TimePicker
                            id={id ? `${id}-time` : undefined}
                            name={name ? `${name}-time` : undefined}
                            value={time}
                            onChange={handleTimeChange}
                            disabled={disabled}
                            error={error}
                            minuteInterval={minuteInterval}
                            roundedClass="rounded-r-md"
                        />
                    </div>
                )}
            </div>
        </div>
    );
};

export default DateTimePicker;
