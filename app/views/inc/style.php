<style>
  /* Amélioration vivacité Soft UI style */

  .card-header {
    background: linear-gradient(90deg, rgba(203,12,159,0.05) 0%, rgba(72,72,241,0.05) 100%) !important;
    border-bottom: 1px solid rgba(0,0,0,0.04) !important;
  }

  .icon-shape {
    transition: all 0.4s ease;
  }

  .icon-shape:hover {
    transform: scale(1.12) rotate(8deg);
  }

  /* Historique timeline-like */
  .history-timeline {
    position: relative;
    padding-left: 2.2rem;
  }

  .history-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 18px;
    width: 3px;
    background: linear-gradient(to bottom, #fd7e14, #2dce89);
  }

  .history-item {
    position: relative;
    margin-bottom: 1.5rem;
  }

</style>