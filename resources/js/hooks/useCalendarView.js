import { DATE_FORMAT } from '@/utils/date';
import dayjs from 'dayjs';
import { useCallback, useMemo, useState } from 'react';
import { useUrlParams } from './useUrlParams';

const VIEW_MONTH = 'month';
const VIEW_WEEK = 'week';

/**
 * Custom hook for managing calendar view state and navigation
 * @param {Object} options Configuration options
 * @param {string} options.initialView Initial view type (month or week)
 * @param {string|dayjs.Dayjs} options.initialDate Initial date
 * @returns {Object} Calendar view state and handlers
 */
export const useCalendarView = ({ initialView = VIEW_MONTH, initialDate = null } = {}) => {
    const [urlParams, setUrlParams] = useUrlParams({
        defaults: {
            view: initialView,
            date: initialDate ? dayjs(initialDate).format(DATE_FORMAT) : dayjs().format(DATE_FORMAT),
        },
        validValues: {
            view: [VIEW_MONTH, VIEW_WEEK],
        },
    });

    const [currentDate, setCurrentDate] = useState(urlParams.date ? dayjs(urlParams.date) : dayjs());
    const [view, setView] = useState(urlParams.view);

    // Sync state changes to URL
    const updateUrlParams = useCallback(
        (newDate, newView) => {
            setUrlParams({
                view: newView,
                date: newDate.format(DATE_FORMAT),
            });
        },
        [setUrlParams]
    );

    /**
     * Navigate to the previous period (month or week)
     */
    const handlePrevious = useCallback(() => {
        setCurrentDate(prev => {
            const newDate = prev.subtract(1, view === VIEW_MONTH ? 'month' : 'week');
            updateUrlParams(newDate, view);
            return newDate;
        });
    }, [view, updateUrlParams]);

    /**
     * Navigate to the next period (month or week)
     */
    const handleNext = useCallback(() => {
        setCurrentDate(prev => {
            const newDate = prev.add(1, view === VIEW_MONTH ? 'month' : 'week');
            updateUrlParams(newDate, view);
            return newDate;
        });
    }, [view, updateUrlParams]);

    /**
     * Navigate to today
     */
    const handleToday = useCallback(() => {
        const today = dayjs();
        setCurrentDate(today);
        updateUrlParams(today, view);
    }, [view, updateUrlParams]);

    /**
     * Change the calendar view type
     * @param {string} newView The new view type (month or week)
     */
    const handleViewChange = useCallback(
        newView => {
            if (![VIEW_MONTH, VIEW_WEEK].includes(newView)) {
                return;
            }
            setView(newView);
            updateUrlParams(currentDate, newView);
        },
        [currentDate, updateUrlParams]
    );

    /**
     * Get formatted title for the current view
     */
    const viewTitle = useMemo(() => {
        if (view === VIEW_MONTH) {
            return currentDate.format('MMMM YYYY');
        }
        return `${currentDate.startOf('week').format('MMM D')} - ${currentDate.endOf('week').format('MMM D, YYYY')}`;
    }, [view, currentDate]);

    return {
        currentDate,
        view,
        viewTitle,
        handlePrevious,
        handleNext,
        handleToday,
        handleViewChange,
        VIEW_MONTH,
        VIEW_WEEK,
    };
};
