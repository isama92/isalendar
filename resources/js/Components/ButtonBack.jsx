const ButtonBack = () => (
    <button
        type="button"
        onClick={() => window.history.back()}
        className="cursor-pointer text-sm/6 font-semibold text-gray-900 dark:text-white"
    >
        {__('common.back')}
    </button>
);

export default ButtonBack;
