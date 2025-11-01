import EventTemplateSelectModal from '@/Components/Calendar/EventTemplateSelectModal';
import { useEventForm } from '@/Hooks';
import { useState } from 'react';
import EventFormActions from './Components/EventFormActions';
import EventFormFields from './Components/EventFormFields';
import EventFormHeader from './Components/EventFormHeader';

const Form = ({ currentUser, calendars, timezones, eventTemplates = [], event = null }) => {
    const [templateModalOpen, setTemplateModalOpen] = useState(false);

    const {
        data,
        setData,
        processing,
        errors,
        isEditing,
        handleStartsAtChange,
        handleTemplateSelect,
        handleSuggestionClick,
        handleSubmit,
    } = useEventForm({ event, currentUser });

    const onTemplateSelect = template => {
        handleTemplateSelect(template);
        setTemplateModalOpen(false);
    };

    return (
        <div className="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <form className="space-y-6">
                <div className="space-y-12 sm:space-y-16">
                    <EventFormHeader
                        isEditing={isEditing}
                        onTemplateClick={() => setTemplateModalOpen(true)}
                        eventTemplatesCount={eventTemplates.length}
                    />

                    <div className="mt-10 space-y-8 border-b border-gray-900/10 pb-12 sm:space-y-0 sm:divide-y sm:divide-gray-900/10 sm:border-t sm:border-t-gray-900/10 sm:pb-0 dark:border-white/10 dark:sm:divide-white/10 dark:sm:border-t-white/10">
                        <EventFormFields
                            data={data}
                            setData={setData}
                            errors={errors}
                            calendars={calendars}
                            timezones={timezones}
                            isEditing={isEditing}
                            handleStartsAtChange={handleStartsAtChange}
                            handleSuggestionClick={handleSuggestionClick}
                        />
                    </div>
                </div>

                <EventFormActions processing={processing} isEditing={isEditing} onSubmit={handleSubmit} />
            </form>

            <EventTemplateSelectModal
                open={templateModalOpen}
                onClose={() => setTemplateModalOpen(false)}
                eventTemplates={eventTemplates}
                onConfirm={onTemplateSelect}
            />
        </div>
    );
};

export default Form;
