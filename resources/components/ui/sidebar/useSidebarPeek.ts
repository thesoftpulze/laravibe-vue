import type { Ref } from "vue"
import { useMutationObserver, useTimeoutFn } from "@vueuse/core"
import { computed, ref, shallowRef, watch } from "vue"

export const SIDEBAR_PEEK_OPEN_DELAY = 150
export const SIDEBAR_PEEK_CLOSE_DELAY = 300

const OPEN_POPUP_TRIGGER = "[aria-haspopup][aria-expanded=\"true\"]"

/**
 * Temporarily expands a collapsed icon sidebar while the mouse, keyboard focus or one of its popups is inside it.
 *
 * A peek is never written to the sidebar cookie. Only an explicit toggle pins the sidebar open or collapses it,
 * and collapsing with the pointer or focus inside holds off the next peek until both have left.
 */
export function useSidebarPeek(open: Ref<boolean>, isMobile: Ref<boolean>) {
  const element = shallowRef<HTMLElement | null>(null)
  const peeking = ref(false)
  const pointerInside = ref(false)
  const focusInside = ref(false)
  const popupOpen = ref(false)
  const suppressed = ref(false)

  const enabled = computed(() => element.value !== null)

  const wantsPeek = computed(() =>
    enabled.value
    && !open.value
    && !isMobile.value
    && !suppressed.value
    && (pointerInside.value || focusInside.value || popupOpen.value),
  )

  const { start: startOpening, stop: stopOpening } = useTimeoutFn(() => {
    peeking.value = true
  }, SIDEBAR_PEEK_OPEN_DELAY, { immediate: false })

  const { start: startClosing, stop: stopClosing } = useTimeoutFn(() => {
    peeking.value = false
  }, SIDEBAR_PEEK_CLOSE_DELAY, { immediate: false })

  watch(wantsPeek, (wants) => {
    if (wants) {
      stopClosing()
      if (!peeking.value) startOpening()
    }
    else {
      stopOpening()
      if (peeking.value) startClosing()
    }
  })

  watch([open, isMobile], () => {
    stopOpening()
    stopClosing()
    peeking.value = false
  })

  useMutationObserver(element, () => {
    popupOpen.value = element.value?.querySelector(OPEN_POPUP_TRIGGER) !== null
  }, { subtree: true, attributes: true, attributeFilter: ["aria-expanded"] })

  function releaseSuppression() {
    if (!pointerInside.value && !focusInside.value) suppressed.value = false
  }

  /**
   * Holds off peeking until the pointer and focus leave, so a sidebar collapsed from inside stays collapsed.
   */
  function suppress() {
    suppressed.value = pointerInside.value || focusInside.value
  }

  function setElement(target: unknown) {
    element.value = target instanceof HTMLElement ? target : null
  }

  const listeners = {
    pointerenter(event: PointerEvent) {
      if (event.pointerType === "mouse") pointerInside.value = true
    },
    pointerleave() {
      pointerInside.value = false
      releaseSuppression()
    },
    focusin(event: FocusEvent) {
      focusInside.value = event.target instanceof HTMLElement && event.target.matches(":focus-visible")
    },
    focusout(event: FocusEvent) {
      if (event.relatedTarget instanceof Node && element.value?.contains(event.relatedTarget)) return

      focusInside.value = false
      releaseSuppression()
    },
  }

  return { enabled, peeking, suppress, setElement, listeners }
}

export type SidebarPeek = ReturnType<typeof useSidebarPeek>
