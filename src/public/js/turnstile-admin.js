$ = jQuery;
$(document).ready(function() {

  let wasSavingPost;
  let wasAutosavingPost;
  let wasPreviewingPost;

  // wp.data.subscribe(function() {
  //   const isSavingPost = wp.data.select( 'core/editor' ).isSavingPost();
  //   const isAutosavingPost = wp.data.select( 'core/editor' ).isAutosavingPost();
  //   const isPreviewingPost = wp.data.select( 'core/editor' ).isPreviewingPost();
  //   const hasActiveMetaBoxes = wp.data.select( 'core/edit-post' ).hasMetaBoxes();
  //
  //   // Save metaboxes on save completion, except for autosaves that are not a post preview.
  //   const shouldTriggerTemplateNotice = (
  //     ( wasSavingPost && ! isSavingPost && ! wasAutosavingPost ) ||
  //     ( wasAutosavingPost && wasPreviewingPost && ! isPreviewingPost )
  //   );
  //
  //   // Save current state for next inspection.
  //   wasSavingPost = isSavingPost;
  //   wasAutosavingPost = isAutosavingPost;
  //   wasPreviewingPost = isPreviewingPost;
  //
  // });

  // wp.data.dispatch( 'core/notices' ).createNotice(
  //   'success', // Can be one of: success, info, warning, error.
  //   'Post published.', // Text string to display.
  //   {
  //     isDismissible: true, // Whether the user can dismiss the notice.
  //     // Any actions the user can perform.
  //     actions: [
  //       {
  //         url: '#',
  //         label: 'View post',
  //       },
  //     ],
  //   }
  // );
});
