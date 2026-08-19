<template>
  <div class="py-5">
    <div
      v-if="editor"
      class="buttons flex items-center flex-wrap gap-x-4 border-t border-l border-r border-gray-400 p-4"
    >
      <button
        type="button"
        @click="editor.chain().focus().toggleBold().run()"
        :disabled="!editor.can().chain().focus().toggleBold().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('bold') }"
      >
        <Icon name="octicon:bold-16" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleItalic().run()"
        :disabled="!editor.can().chain().focus().toggleItalic().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('italic') }"
      >
        <Icon name="octicon:italic-16" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleUnderline().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('underline') }"
      >
        <Icon name="mingcute:underline-fill" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleStrike().run()"
        :disabled="!editor.can().chain().focus().toggleStrike().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('strike') }"
      >
        <Icon name="gg:format-strike" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleCode().run()"
        :disabled="!editor.can().chain().focus().toggleCode().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('code') }"
      >
        <Icon name="solar:code-bold" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().unsetAllMarks().run()"
      >
        <Icon name="codicon:clear-all" />
      </button>
      <button type="button" @click="editor.chain().focus().clearNodes().run()">
        <Icon name="fa6-solid:circle-nodes" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().setParagraph().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('paragraph') }"
      >
        <Icon name="teenyicons:paragraph-solid" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 1 }).run()"
        :class="{
          'bg-gray-200 rounded px-1.5': editor.isActive('heading', {
            level: 1,
          }),
        }"
      >
        <Icon name="ci:heading-h1" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 2 }).run()"
        :class="{
          'bg-gray-200 rounded px-1.5': editor.isActive('heading', {
            level: 2,
          }),
        }"
      >
        <Icon name="ci:heading-h2" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 3 }).run()"
        :class="{
          'bg-gray-200 rounded px-1.5': editor.isActive('heading', {
            level: 3,
          }),
        }"
      >
        <Icon name="ci:heading-h3" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 4 }).run()"
        :class="{
          'bg-gray-200 rounded px-1.5': editor.isActive('heading', {
            level: 4,
          }),
        }"
      >
        <Icon name="ci:heading-h4" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 5 }).run()"
        :class="{
          'bg-gray-200 rounded px-1.5': editor.isActive('heading', {
            level: 5,
          }),
        }"
      >
        <Icon name="ci:heading-h5" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleHeading({ level: 6 }).run()"
        :class="{
          'bg-gray-200 rounded px-1.5': editor.isActive('heading', {
            level: 6,
          }),
        }"
      >
        <Icon name="ci:heading-h6" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleBulletList().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('bulletList') }"
      >
        <Icon name="streamline:bullet-list-solid" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleOrderedList().run()"
        :class="{
          'bg-gray-200 rounded px-1.5': editor.isActive('orderedList'),
        }"
      >
        <Icon name="icon-park-outline:ordered-list" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleCodeBlock().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('codeBlock') }"
      >
        <Icon name="fluent:code-block-32-regular" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().toggleBlockquote().run()"
        :class="{ 'bg-gray-200 rounded px-1.5': editor.isActive('blockquote') }"
      >
        <Icon name="mingcute:blockquote-fill" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().setHorizontalRule().run()"
      >
        <Icon name="codicon:horizontal-rule" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().setHardBreak().run()"
      >
        <Icon name="bi:file-earmark-break" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().undo().run()"
        :disabled="!editor.can().chain().focus().undo().run()"
      >
        <Icon name="material-symbols:undo" />
      </button>
      <button
        type="button"
        @click="editor.chain().focus().redo().run()"
        :disabled="!editor.can().chain().focus().redo().run()"
      >
        <Icon name="material-symbols:redo" />
      </button>
    </div>

    <TiptapEditorContent :editor="editor" />
  </div>
</template>

<script setup>
import StarterKit from "@tiptap/starter-kit";
import Strike from "@tiptap/extension-strike";
import { Editor } from "@tiptap/vue-3";
import Underline from "@tiptap/extension-underline";
import { markPasteRule } from "@tiptap/core";

// New:
const pasteRegex = /(?:^|\s)((?:~)((?:[^~]+))(?:~))/g;

const CustomStrike = Strike.extend({
  addPasteRules() {
    return [
      markPasteRule({
        find: pasteRegex,
        type: this.type,
      }),
    ];
  },
});

const props = defineProps({
  modelValue: {
    type: String,
    default: "",
  },
});

const emits = defineEmits(["update:modelValue"]);

const editor = ref(null);

watch(
  () => props.modelValue,
  (value) => {
    const isSame = editor.value.getHTML() === value;

    if (!isSame) {
      editor.value.commands.setContent(value, false);
    }
  }
);

const updateModelValue = () => {
  emits("update:modelValue", editor.value.getHTML());
};

const destroyEditor = () => {
  editor.value.destroy();
};

const onUpdate = () => {
  updateModelValue();
};

editor.value = new Editor({
  editorProps: {
    attributes: {
      class:
        "border border-gray-400 p-4 min-h-[16rem] max-h-[16rem] overflow-y-auto prose-p:leading-3  prose prose-sm md:prose-lg lg:prose-xl xl:prose-2xl mx-auto focus:outline-none ",
    },
    // transformPastedText(text) {
    //   return text.toUpperCase();
    // },
  },
  extensions: [TiptapStarterKit, Underline, markPasteRule],
  content: props.modelValue,
  onUpdate: onUpdate,
});

onBeforeUnmount(destroyEditor);
</script>

<style lang="scss">
/* Basic editor styles */
.tiptap {
  > * + * {
    margin-top: 0.75em;
  }

  code {
    background-color: rgba(#616161, 0.1);
    color: #616161;
  }
}

.content {
  padding: 1rem 0 0;

  h3 {
    margin: 1rem 0 0.5rem;
  }

  pre {
    border-radius: 5px;
    color: #333;
  }

  code {
    display: block;
    white-space: pre-wrap;
    font-size: 0.8rem;
    padding: 0.75rem 1rem;
    background-color: #e9ecef;
    color: #495057;
  }
}
</style>
