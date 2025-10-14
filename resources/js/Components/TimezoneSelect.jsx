import Select from 'react-select';

const TimezoneSelect = ({ id, name, value, onChange, options, error, className = '' }) => {
    return (
        <div className={className}>
            <Select
                id={id}
                name={name}
                value={value ? { value: value, label: value } : null}
                onChange={onChange}
                options={options}
                isSearchable
                aria-invalid={!!error}
                aria-describedby={error ? `${id}-error` : ''}
                classNames={{
                    control: () => 'rounded-md',
                    menu: () => 'rounded-md shadow-lg mt-1',
                    option: () => 'cursor-pointer px-3 py-2',
                    groupHeading: () => 'text-sm font-semibold px-3 py-2 uppercase',
                }}
                styles={{
                    control: (base, state) => ({
                        ...base,
                        backgroundColor: 'rgb(var(--color-bg-control))',
                        borderColor: state.isFocused ? 'rgb(99 102 241)' : 'rgb(var(--color-border-control))',
                        borderWidth: '1px',
                        boxShadow: state.isFocused ? '0 0 0 2px rgb(99 102 241 / 0.2)' : 'none',
                        '&:hover': {
                            borderColor: state.isFocused ? 'rgb(99 102 241)' : 'rgb(var(--color-border-control))',
                        },
                    }),
                    menu: base => ({
                        ...base,
                        backgroundColor: 'rgb(var(--color-bg-menu) / 1)',
                        border: '1px solid rgb(var(--color-border-menu) / 1)',
                    }),
                    option: (base, state) => ({
                        ...base,
                        backgroundColor: state.isSelected
                            ? 'rgb(99 102 241)'
                            : state.isFocused
                              ? 'rgb(var(--color-bg-option-hover) / 1)'
                              : 'transparent',
                        color: state.isSelected ? 'white' : 'rgb(var(--color-text) / 1)',
                        '&:hover': {
                            backgroundColor: state.isSelected
                                ? 'rgb(99 102 241)'
                                : 'rgb(var(--color-bg-option-hover) / 1)',
                        },
                    }),
                    input: base => ({
                        ...base,
                        color: 'rgb(var(--color-text) / 1)',
                    }),
                    singleValue: base => ({
                        ...base,
                        color: 'rgb(var(--color-text) / 1)',
                    }),
                    groupHeading: base => ({
                        ...base,
                        color: 'rgb(var(--color-text-muted) / 1)',
                    }),
                }}
            />
            {error && (
                <p id={`${id}-error`} role="alert" className="mt-2 text-sm text-red-600 dark:text-red-400">
                    {error}
                </p>
            )}
        </div>
    );
};

export default TimezoneSelect;
