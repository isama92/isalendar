const EventFormHeader = ({ isEditing, onTemplateClick, eventTemplatesCount }) => {
    return (
        <div className="flex items-center justify-between">
            <h2 className="text-base/7 font-semibold text-gray-900 dark:text-white">
                {isEditing
                    ? __('actions.named.edit', { name: __('events.event') })
                    : __('actions.named.create', { name: __('events.event') })}
            </h2>
            {!isEditing && (
                <button
                    type="button"
                    onClick={onTemplateClick}
                    disabled={eventTemplatesCount === 0}
                    className="inline-flex cursor-pointer items-center gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-700 dark:text-gray-100 dark:inset-ring-gray-600 dark:hover:bg-gray-600"
                >
                    {__('events.use_event_template')}
                </button>
            )}
        </div>
    );
};

export default EventFormHeader;
