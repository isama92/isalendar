import { EditorContent, useEditor } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import clsx from 'clsx';
import { useEffect } from 'react';

const RichTextEditor = ({ value, onChange, error, id, name, placeholder }) => {
    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                heading: false,
                code: false,
                codeBlock: false,
                blockquote: false,
                horizontalRule: false,
                strike: false,
            }),
        ],
        content: value || '',
        editorProps: {
            attributes: {
                class: clsx(
                    'prose prose-sm max-w-none focus:outline-none min-h-[80px] p-3',
                    'dark:prose-invert',
                    'prose-p:my-2 prose-ul:my-2 prose-ol:my-2 prose-li:my-0',
                    error ? 'text-red-900 dark:text-red-400' : 'text-gray-900 dark:text-white'
                ),
            },
        },
        onUpdate: ({ editor }) => {
            onChange(editor.getHTML());
        },
    });

    useEffect(() => {
        if (editor && value !== editor.getHTML()) {
            editor.commands.setContent(value || '');
        }
    }, [value, editor]);

    if (!editor) {
        return null;
    }

    return (
        <div className="w-full sm:max-w-2xl">
            <div
                className={clsx(
                    'rounded-md bg-white outline-1 -outline-offset-1 focus-within:outline-2 focus-within:-outline-offset-2 dark:bg-white/5',
                    error
                        ? 'outline-red-300 focus-within:outline-red-600 dark:outline-red-500/50 dark:focus-within:outline-red-400'
                        : 'outline-gray-300 focus-within:outline-indigo-600 dark:outline-white/10 dark:focus-within:outline-indigo-500'
                )}
                aria-invalid={!!error}
                aria-describedby={error ? `${id}-error` : ''}
            >
                <div className="flex flex-wrap gap-1 border-b border-gray-200 p-2 dark:border-white/10">
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBold().run()}
                        disabled={!editor.can().chain().focus().toggleBold().run()}
                        className={clsx(
                            'rounded px-2 py-1 text-sm font-semibold transition-colors',
                            editor.isActive('bold')
                                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                                : 'text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:text-gray-300 dark:hover:bg-white/5'
                        )}
                        title="Bold"
                    >
                        B
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleItalic().run()}
                        disabled={!editor.can().chain().focus().toggleItalic().run()}
                        className={clsx(
                            'rounded px-2 py-1 text-sm italic transition-colors',
                            editor.isActive('italic')
                                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                                : 'text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:text-gray-300 dark:hover:bg-white/5'
                        )}
                        title="Italic"
                    >
                        I
                    </button>
                    <div className="mx-1 w-px bg-gray-300 dark:bg-white/10" />
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleBulletList().run()}
                        className={clsx(
                            'rounded px-2 py-1 text-sm transition-colors',
                            editor.isActive('bulletList')
                                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                                : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5'
                        )}
                        title="Bullet List"
                    >
                        •&nbsp;List
                    </button>
                    <button
                        type="button"
                        onClick={() => editor.chain().focus().toggleOrderedList().run()}
                        className={clsx(
                            'rounded px-2 py-1 text-sm transition-colors',
                            editor.isActive('orderedList')
                                ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-300'
                                : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5'
                        )}
                        title="Numbered List"
                    >
                        1.&nbsp;List
                    </button>
                </div>
                <EditorContent editor={editor} id={id} name={name} placeholder={placeholder} />
            </div>
        </div>
    );
};

export default RichTextEditor;
