<style lang="scss">
.select-city {
    &__title {
        font-size: 16px;
        &-caption {
            color: $gray-500;
        }
    }
    &__selected-name {
        font-weight: 600;
        &:after {
           @include fa('caret-down');
        }
    }
}
</style>