<?php
/**
 * Class GridDataProvider
 *
 * @project         <The PHP 7 Grid-Data Library>
 * @package         gdgrid/gd
 * @license         MIT License
 * @copyright       Copyright (c) 2018, GD Lab
 * @author          GD Lab <dev.gdgrid@gmail.com>
 * @author-website  <>
 * @project-website <>
 * @github          https://github.com/gdgrid/gd
 */

namespace gdgrid\gd
{

    use RuntimeException;

    class GridDataProvider implements IGridFormProvider, IGridTableProvider
    {
        const DATA_STATEMENT = [
            'fields'           => [],
            'safeFields'       => [],
            'requiredFields'   => [],
            'inputTypes'       => [],
            'inputSizes'       => [],
            'inputOptions'     => [],
            'inputPrompts'     => [],
            'inputErrors'      => [],
            'tableCellPrompts' => [],
        ];

        protected $data = [];

        /**
         * IGridData Provider
         **/
        protected $dataProvider;

        /**
         * Data Object
         **/
        protected $entity;

        /**
         * Array of Data Objects
         **/
        protected $entityItems;

        /**
         * GridDataProvider constructor.
         *
         * @param object $entity
         * @throws RuntimeException
         */
        public function __construct($entity)
        {
            $this->setEntity($entity);

            $this->mergeData([]);
        }

        final function setDataProvider(IGridData $provider)
        {
            $this->dataProvider = $provider;

            return $this;
        }

        /**
         * @return IGridData
         */
        public function getDataProvider()
        {
            return $this->dataProvider;
        }

        /**
         * @param object $entity
         *
         * @return $this
         * @throws RuntimeException
         */
        public function setEntity($entity)
        {
            if (false === empty($this->entity))

                throw new RuntimeException('The Entity is already set.');

            if (false === get_class($entity))

                throw new RuntimeException('The Entity is not a valid class object.');

            $this->entity = $entity;

            return $this;
        }

        /**
         * @return object
         */
        public function getEntity()
        {
            return $this->entity;
        }

        public function getItems()
        {
            return $this->entityItems ?? [];
        }

        public function setItems(array $items)
        {
            $this->entityItems = $items;

            return $this;
        }

        public function checkData(string $key)
        {
            if (false == array_key_exists($key, self::DATA_STATEMENT))

                throw new RuntimeException(sprintf('The data key `%s` is not found in GridDataProvider.', $key));
        }

        protected function checkDataKeys(array $data)
        {
            foreach (array_keys($data) as $key)
            {
                $this->checkData($key);
            }
        }

        public function setData(array $data)
        {
            $this->checkDataKeys($data);

            $this->data = array_merge(self::DATA_STATEMENT, $data);

            return $this;
        }

        public function mergeData(array $data)
        {
            $this->checkDataKeys($data);

            $this->data = array_merge(self::DATA_STATEMENT, $this->getEntityData($this->entity), $data);

            return $this;
        }

        public function replaceData(array $data)
        {
            $this->mergeData($data);

            foreach ($data as $k => $v) $this->data[$k] = $v;

            return $this;
        }

        protected function getEntityData($entity)
        {
            $data = [];

            if ($entity instanceof IGridFormProvider)

                $data = [
                    'fields'       => $entity->gridFields(),
                    'safeFields'   => $entity->gridSafeFields(),
                    'inputTypes'   => $entity->gridInputTypes(),
                    'inputSizes'   => $entity->gridInputSizes(),
                    'inputOptions' => $entity->gridInputOptions(),
                    'inputPrompts' => $entity->gridInputPrompts(),
                    'inputErrors'  => $entity->gridInputErrors(),
                ];

            if ($entity instanceof IGridTableProvider)

                $data['tableCellPrompts'] = $entity->gridTableCellPrompts();

            return $data;
        }

        public function fetchData()
        {
            foreach ($this->getDataProvider()->fetchFields() as $field)
            {
                $this->data['fields'][$field['field']] = $field['name'];

                if ($field['type'] === GridForm::DEFAULT_INPUT_TYPE) $field['type'] = 'textarea';

                $this->data['inputTypes'][$field['field']] = $field['type'];

                if (false == empty($field['size'])) $this->data['inputSizes'][$field['field']] = $field['size'];

                if (false == empty($field['prompt'])) $this->data['inputPrompts'][$field['field']] = $field['prompt'];

                if (false == empty($field['required'])) $this->data['requiredFields'][] = $field['field'];

                $this->fetchDataField($field);
            }

            return $this;
        }

        protected function fetchDataField(array $field)
        {
        }

        public function gridFields(): array
        {
            return $this->data['fields'];
        }

        public function gridSafeFields(): array
        {
            return $this->data['safeFields'];
        }

        public function unSafeFields(array $fields)
        {
            $this->data['safeFields'] = array_diff($this->data['safeFields'], $fields);

            return $this;
        }

        public function requiredFields(): array
        {
            return $this->data['requiredFields'];
        }

        public function gridInputTypes(): array
        {
            return $this->data['inputTypes'];
        }

        public function gridInputOptions(): array
        {
            return $this->data['inputOptions'];
        }

        public function gridInputSizes(): array
        {
            return $this->data['inputSizes'];
        }

        public function gridInputPrompts(): array
        {
            return $this->data['inputPrompts'];
        }

        public function gridInputErrors(): array
        {
            return $this->data['inputErrors'];
        }

        public function gridTableCellPrompts()
        {
            return $this->data['tableCellPrompts'];
        }

        public function __get(string $prop)
        {
            return $this->entity->{$prop};
        }
    }
}
